<?php
/**
 * Created by PhpStorm.
 * User: amiel
 * Date: 25/06/19
 * Time: 16:17
 */

namespace GenericTools\Helpers;


use Zend\Db\Exception\ErrorException;

class ArrivalDaysParserHelper
{
    /*
    //  Events that repeat at a certain frequency
    //  Every,Every Other,Every Third,Every Fourth
    //  Day,Week,Month,Year,MWF,TR,M-F,SS
    */
    const REGULAR_RECURRING_ID = 1;

    /*
    //  Events that repeat on certain parameters
    //  On 1st,2nd,3rd,4th,Last
    //  Sun,Mon,Tue,Wed,Thu,Fri,Sat
    //  Every N Months
    */
    const RECURRING_ON_ID = 2;

    /*
    //  Events that repeat on fixed days in every week
    */
    const RECURRING_EVERY_WEEK_ID = 3;

    const REPEAT_EVERY_DAY = 0;
    const REPEAT_EVERY_WEEK = 1;
    const REPEAT_EVERY_MONTH = 2;
    const REPEAT_EVERY_YEAR = 3;
    const REPEAT_EVERY_WORK_DAY = 4;
    const REPEAT_DAYS_EVERY_WEEK = 6;

    const REPEAT_EVERY_DAY_STRING = 'day';
    const REPEAT_EVERY_WEEK_STRING = 'week';
    const REPEAT_EVERY_MONTH_STRING = 'month';
    const REPEAT_EVERY_YEAR_STRING = 'year';
    const REPEAT_EVERY_WORK_DAY_STRING = 'work day';
    const REPEAT_DAYS_EVERY_WEEK_STRING = 'week';

    private $recurrEvent;

    /**
     * parse the serialize spec into array - $recurrEvent
     * @param $type
     * @param $spec
     */
    public function parse($type, $spec, $eventDate)
    {
        $frequencyDetails = unserialize($spec);
        if (!$this->checkEvent($type, $frequencyDetails)) {
            throw new ErrorException('The serialized spec is not valid');
        };

        switch ($type) {
            case self::REGULAR_RECURRING_ID:
            case self::RECURRING_EVERY_WEEK_ID:
                $this->parseRegular($frequencyDetails, $eventDate);
                break;
            case self::RECURRING_ON_ID:
                $this->parseRepeatOn($frequencyDetails);
                break;

        }
        return $this;
    }

    /**
     * return readable string for every type pf recursive event
     * Checked and developed only for Hebrew
     * @return string
     * @throws ErrorException
     */
    public function formatString()
    {
        if (is_null($this->recurrEvent)) {
            throw new ErrorException('Must use parse() before formatString()');
        }

        switch ($this->recurrEvent['every']) {
            case self::REPEAT_EVERY_DAY_STRING:
                if ($this->recurrEvent['freq'] > 1) {
                    return xlt('every') . ' ' . $this->recurrEvent['freq'] . ' ' . xlt('days');
                } else {
                    return xlt('every day');
                }
                break;
            case self::REPEAT_EVERY_WORK_DAY_STRING:
                if ($this->recurrEvent['freq'] > 1) {
                    return xlt('every') . ' ' . $this->recurrEvent['freq'] . ' ' . xlt('work days');
                } else {
                    return xlt('every work day');
                }
                break;
            case self::REPEAT_DAYS_EVERY_WEEK_STRING:
                $daysNames = array(1 => xl('Su{{Sunday}}') , 2 => xl('Mo{{Monday}}'), 3 => xl('Tu{{Tuesday}}'), 4 => xl('We{{Wednesday}}'),
                                   5 => xl('Th{{Thursday}}'), 6 => xl('Fr{{Friday}}'), 7 => xl('Sa{{Saturday}}'));

                foreach ($this->recurrEvent['days'] as $day) {
                    $days[] = $daysNames[$day];
                }
                if (count($this->recurrEvent['days']) > 1) {
                    $string = xlt('days');
                } else {
                    $string = xlt('day');
                }
                $days = implode(',', $days);


                if ($this->recurrEvent['freq'] > 1) {
                     $string .= ' ' . $days . ' ' . xlt('every') . ' ' . $this->recurrEvent['freq'] . ' ' . xlt('weeks') ;
                } else {
                    $string .= ' ' . $days;
                }
                return $string;
                break;
            case self::REPEAT_EVERY_MONTH_STRING:
                if (is_null($this->recurrEvent['weekInMonth']) && is_array($this->recurrEvent['date'])) {
                    if ($this->recurrEvent['freq'] > 1) {
                        return  $this->recurrEvent['date']['dayOfMonth'] . ' ' . xlt('of month')  . ' ' . xlt('every') . ' ' . $this->recurrEvent['freq'] . ' ' . xlt('months');
                    } else {
                        return  xlt('every') . ' ' . $this->recurrEvent['date']['dayOfMonth'] . ' ' . xlt('of month');
                    }
                } else {
                    $daysNames = array(1 => xl('Su{{Sunday}}') , 2 => xl('Mo{{Monday}}'), 3 => xl('Tu{{Tuesday}}'), 4 => xl('We{{Wednesday}}'),
                                       5 => xl('Th{{Thursday}}'), 6 => xl('Fr{{Friday}}'), 7 => xl('Sa{{Saturday}}'));
                    $day = $daysNames[$this->recurrEvent['days']];
                    $weekInMonth = $this->recurrEvent['weekInMonth'] === 'last' ? xlt('last') : $this->recurrEvent['weekInMonth'];
                    $string =  xlt('every') . ' ' . xlt('day') . ' ' . $day . ' ' . xlt('in week') . ' ' . $weekInMonth . ' ' . xlt('of the month');
                    if ($this->recurrEvent['freq'] > 1) {
                        return $string .= ' ' . xlt('every') . ' ' . $this->recurrEvent['freq'] . ' ' . xlt('months');
                    }
                    return $string;
                }
                break;
            case self::REPEAT_EVERY_YEAR_STRING:
                $string = $this->recurrEvent['date']['dayOfMonth'] . '/'. $this->recurrEvent['date']['month'] . ' ' . xlt('every');
                if ($this->recurrEvent['freq'] > 1) {
                    $string .= ' ' . $this->recurrEvent['freq'] . ' ' . xlt('years');
                } else {
                    $string .= ' ' . xlt('year');
                }
                return $string;
                break;
        }
    }

    /**
     * Return the setting of event as array
     * array array(
            'every' => day/week/month/year (string),
            'freq' => numeric bigger then 1 ,
            'days' => array of int 1-7 (optional) ,
            'date' => array ('dayOfMonth' =>,'month' =>) required for month or year
            'weekInMonth' => int (require for 'repeat on' option - type 2)
        );
     * @return mixed
     * @throws ErrorException
     */
    public function formatArray()
    {
        if (!is_null($this->recurrEvent)) {
            return $this->recurrEvent;
        } else {
            throw new ErrorException('Must use parse() before formatArray()');
        }
    }

    /**
     * parse psec to array fot type 1 or 3
     * @param array $spec
     * @param       $eventDate
     *
     * @throws ErrorException
     */
    private function parseRegular(array $spec, $eventDate)
    {

        switch ($spec['event_repeat_freq_type']) {
            case self::REPEAT_EVERY_DAY:
                $every = self::REPEAT_EVERY_DAY_STRING;
                break;
            case self::REPEAT_EVERY_WEEK:
                $every = self::REPEAT_DAYS_EVERY_WEEK_STRING;
                break;
            case self::REPEAT_EVERY_MONTH:
                $every = self::REPEAT_EVERY_MONTH_STRING;
                break;
            case self::REPEAT_EVERY_YEAR:
                $every = self::REPEAT_EVERY_YEAR_STRING;
                break;
            case self::REPEAT_EVERY_WORK_DAY:
                $every = self::REPEAT_EVERY_WORK_DAY_STRING;
                break;
            case self::REPEAT_DAYS_EVERY_WEEK:
                $every = self::REPEAT_DAYS_EVERY_WEEK_STRING;
                break;
            default:
                throw new ErrorException('Repeat type is not familiar');
        }

        switch ($spec['event_repeat_freq_type']) {
            case self::REPEAT_EVERY_MONTH:
            case self::REPEAT_EVERY_YEAR:
                $date['dayOfMonth'] = date('j', strtotime($eventDate));
                $date['month'] = date('n', strtotime($eventDate));
                $this->setRecurrEvent($every, $spec['event_repeat_freq'],null, $date);
                break;
            case self::REPEAT_DAYS_EVERY_WEEK:
                if (is_numeric($spec['event_repeat_freq'])) {
                    $days = [$spec['event_repeat_freq']];
                } else {
                    $days = explode(',', $spec['event_repeat_freq']);
                }
                $this->setRecurrEvent($every, 1, $days);
                break;
            case self::REPEAT_EVERY_WEEK:
                $day = date('w', strtotime($eventDate)) + 1;
                $this->setRecurrEvent($every, $spec['event_repeat_freq'], [$day]);
                break;
            case self::REPEAT_EVERY_DAY:
            case self::REPEAT_EVERY_WORK_DAY:
                $this->setRecurrEvent($every, $spec['event_repeat_freq']);
                break;
            default:
                throw new ErrorException('Repeat type is not familiar');
        }

    }

    /**
     * parse spec to array for type 2
     * @param array $spec
     */
    private function parseRepeatOn(array $spec)
    {
        $weekInMonth = $spec['event_repeat_on_num'] == 5 ? 'last' : $spec['event_repeat_on_num'];
        $this->setRecurrEvent(self::REPEAT_EVERY_MONTH_STRING, $spec['event_repeat_on_freq'], $spec['event_repeat_on_day'] + 1, null, $weekInMonth);
    }

    private function setRecurrEvent($every, $freq , $days = null, $date = null, $weekInMonth = null)
    {
        $this->recurrEvent = array(
            'every' => $every,
            'freq' => $freq,
            'days' => $days,
            'date' => $date,
            'weekInMonth' => $weekInMonth
        );
    }


    /**
     * check if recurring spec is valid
     * @param $type
     * @param $recurrspec
     *
     * @return bool
     */
    private function checkEvent($type, $recurrspec)
    {
        $validSpec = true;

        switch ($type) {
            case  self::REGULAR_RECURRING_ID:
            case  self::RECURRING_EVERY_WEEK_ID:
                if (empty($recurrspec['event_repeat_freq']) || !isset($recurrspec['event_repeat_freq_type'])) {
                    $validSpec = false;
                }
                break;
            case  self::RECURRING_ON_ID:
                if (empty($recurrspec['event_repeat_on_freq']) || empty($recurrspec['event_repeat_on_num']) || !isset($recurrspec['event_repeat_on_day'])) {
                    $validSpec = false;
                }
                break;
        }

        return $validSpec;
    }



}