<?php
/**
 * User: Eyal Wolanowski eyalvo@matrix.co.il
 * Date: 17/05/20
 * Time: 16:58
 */

namespace GenericTools\Service;

/**
 * Date: 13/05/20
 * @author  Eyal Wolanowski <eyal.wolanowski@gmail.com>
 * This class is a simple and custom implementation of Server-Sent Events (SSE) communications protocol
 */
class SseService

{
    /**
     * CLIENT_TIMEOUT is the maximum time the client wait for response
     * The value is in ### MILLISECONDS ###
     */
    CONST CLIENT_TIMEOUT = 1800000;

    /**
     * CLIENT_RETRY_DELAY is the time the client wait untill it tries to reconnect
     * The value is in ### MILLISECONDS ###
     */
    CONST CLIENT_RETRY_DELAY = 1000;

    /**
     * CONNECT_TIME_LIMIT is the maximum time the server main loop will run
     * The value is in ### SECONDS ###
     */
    CONST CONNECT_TIME_LIMIT = 1200000;

    /**
     * EVENT_LOOP_INTERVAL is the time the server wait until it sends a response
     * A response can be a :heartbeat or contain :data  :id
     * The value is in ### SECONDS ###
     */
    CONST EVENT_LOOP_INTERVAL = 3;

    /**
     * EVENT_LOOP_EXEC_LIMIT is the time the server wait until it get result from db
     * The value is in ### SECONDS ###
     */
    CONST EVENT_LOOP_EXEC_LIMIT = 120;

    private $client_timeout = null;
    private $client_retry_delay = null;
    private $connect_time_limit = null;
    private $event_loop_interval = null;
    private $event_loop_exec_limit = null;
    private $eventId = null;
    private $updateClass = null;
    private $updateMethod = null;
    private $updateParam = null;
    private $timeStamp = null;

    /**
     * @param \stdClass that handle the update check
     * @param string of method name that handle the update check
     * @param string value passed to update method
     * @param array SSE time configurations
     */
    public function __construct($updateClass, $updateMethod, $updateParam, $config = array())
    {
        $this->updateClass = $updateClass;
        $this->updateMethod = $updateMethod;
        $this->updateParam = $updateParam;
        $this->eventId = 0;

        if (!empty($config)) {
            $this->client_timeout = ($config['client_timeout']) ? $config['client_timeout'] : self::CLIENT_TIMEOUT;
            $this->client_retry_delay = ($config['client_retry_delay']) ? $config['client_retry_delay'] : self::CLIENT_RETRY_DELAY;
            $this->connect_time_limit = ($config['connect_time_limit']) ? $config['connect_time_limit'] : self::CONNECT_TIME_LIMIT;
            $this->event_loop_interval = ($config['event_loop_interval']) ? $config['event_loop_interval'] : self::EVENT_LOOP_INTERVAL;
            $this->event_loop_exec_limit = ($config['event_loop_exec_limit']) ? $config['event_loop_exec_limit'] : self::EVENT_LOOP_EXEC_LIMIT;
        } else {
            $this->client_timeout = self::CLIENT_TIMEOUT;
            $this->client_retry_delay = self::CLIENT_RETRY_DELAY;
            $this->connect_time_limit = self::CONNECT_TIME_LIMIT;
            $this->event_loop_interval = self::EVENT_LOOP_INTERVAL;
            $this->event_loop_exec_limit = self::EVENT_LOOP_EXEC_LIMIT;
        }

    }

    /**
     *  Main function to start the SSE
     */
    public function connect()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        header('Content-Type: text/event-stream');
        header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
        header("Pragma: no-cache"); // HTTP 1.0.


        echo "heartbeatTimeout: " . $this->client_timeout . "\n\n";
        echo "retry: " . $this->client_retry_delay . "\n\n";
        ob_flush();
        flush();

        $expirationTime = time() + $this->connect_time_limit;

        $this->updateTimeStamp();
        while (time() < $expirationTime) {
            $this->doEventLoop();
            sleep($this->event_loop_interval);
        }

        /**
         * When CONNECT_TIME_LIMIT (server max run time) ends
         * send 204 response to the client
         */
        ob_start();
        header("HTTP/1.1 204 NO CONTENT",true);
        header("Expires: 0",true);   // Proxies.
        ob_end_flush();         //now the headers are sent

    }

    protected function updateTimeStamp()
    {
        $this->timeStamp = date('Y-m-d H:i:s');
    }

    protected function doEventLoop()
    {
        $sentUpdate = false;
        set_time_limit($this->event_loop_exec_limit);

        $dbLastUpdateDate = $this->getUpdate();
        $updateFlag = $this->checkForUpdate($dbLastUpdateDate);
        if ($updateFlag) {
            ++$this->eventId;
            $this->updateTimeStamp();
            $data = json_encode([
                'event' => "update",
                'info' => "true"
            ]);
            echo "id: {$this->eventId}\n";
            echo "data: {$data}\n\n";
            ob_flush();
            flush();
            $sentUpdate = true;
        }
        if (!$sentUpdate) {
            echo ": heartbeat\n\n";
            ob_flush();
            flush();
        }
    }

    protected function checkForUpdate($dbDate)
    {
        $dbDateStamp = strtotime($dbDate);
        $lastUpdateStamp = strtotime($this->getTimeStamp());
        // Compare the timestamp date
        if ($dbDateStamp > $lastUpdateStamp) {
            return true;
        } else {
            return false;
        }
    }

    protected function getUpdate()
    {
        return $this->updateClass->{$this->updateMethod}($this->updateParam);
    }

    protected function getTimeStamp()
    {
        return $this->timeStamp;
    }

}
