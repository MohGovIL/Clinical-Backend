<?php
/* +-----------------------------------------------------------------------------+
*    OpenEMR - Open Source Electronic Medical Record
*    Copyright (C) 2013 Z&H Consultancy Services Private Limited <sam@zhservices.com>
*
*    This program is free software: you can redistribute it and/or modify
*    it under the terms of the GNU Affero General Public License as
*    published by the Free Software Foundation, either version 3 of the
*    License, or (at your option) any later version.
*
*    This program is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*    GNU Affero General Public License for more details.
*
*    You should have received a copy of the GNU Affero General Public License
*    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*    @author  Basil PT <basil@zhservices.com>
* +------------------------------------------------------------------------------+
*/
namespace Formhandler\Plugin;

use Formhandler\Controller\FormhandlerController;
use OpenEMR\Common\Crypto\CryptoGen;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Application\Model\ApplicationTable;



class CouchDBHandle extends AbstractPlugin
{

  /**
   *
   * Documents Table Object
   * @param type $sm Service Manager
   **/
    const DOCUMENT="document";
    const LABEL="label";
    const ID="_id";
    const SQL="sql";
    const DRAFT="draft";
    const VALIDATION="validators";
    const TABLE_VALIDATION="table_validators";
    const CONDITIONAL="conditional";
    const FIELDS="fields";
    const ATTRIBUTES="attributes";
    const TYPE="type";
    const INPUT_ID="id";
    const REQUIRED="required";
    const NAME="name";
    const CONDITIONS="conditions";
    const JSADDONS="jsAddons";
    const SQL_MUST_PARM_AMOUNT=6;
    const NEW_FORM_DIR_PATH="/forms/";
    const FORM_NAME_PLACEHOLDER = "#FORMNAME#";
    const FORM_TITLE_PLACEHOLDER= "#FORMTITLE#";
    const FORM_SQL_PLACEHOLDER="#SQL#";
    const INFO_FILE="info.txt";
    const NEW_FILE="new.php";
    const REPORT_FILE="report.php";
    const TABLE_FILE="table.sql";
    const VIEW_FILE="view.php";
    const VALIDATORS="validators";



  public function __construct()
  {
  }


	/**
	 * couchDB - Couch DB Connection
	 * 				 - Uses Doctrine  CouchDBClient
	 * @return Object $connection
	 */
	public function couchDBConnection()
	{
		$host       = $GLOBALS['couchdb_host'];
		$port       = $GLOBALS['couchdb_port'];
		$usename    = $GLOBALS['couchdb_user'];
        $cryptoGen = new CryptoGen();
		$password   = $cryptoGen->decryptStandard($GLOBALS['couchdb_pass']);
		$database	= $GLOBALS['couchdb_dbase'];
        //todo - in the production create one database for all the installation (called 'moh_formhandler)
        //$database = "moh_formhandler";
		$enable_log = ($GLOBALS['couchdb_log'] == 1) ? true : false;

		$options = array(
			'host' 		  => $host,
			'port' 		  => $port,
			'user' 		  => $usename,
			'password' 	  => $password,
			'logging' 	  => $enable_log,
			'dbname'	  => $database
		);
		$connection = \Doctrine\CouchDB\CouchDBClient::create($options);
		return $connection;
	}

	/**
	 * saveCouchDocument - Save Document to Couch DB
	 * @param Object $connection Couch DB Connection Object
	 * @param Json Encoded Data
	 * @return Array
	 */
	public function saveCouchDocument($data,$my_id,$connection)
	{
	    $connection=$this->couchDBConnection();
		//$couch 	= $connection->postDocument($data);


        $id			= $my_id;//$couch[0];
         $connection->putDocument($data,$id);
        return $couch;



		$id			= $my_id;//$couch[0];
		$rev		= $couch[1];
		if($id && $rev) {



			$connection->putDocument($data,$id,$rev);
			return $couch;
		} else {
			return false;
		}
	}

	/**
	 * getDocument Retieve Documents from Couch/HDD
	 * @param Integer $documentId Document ID
	 * @param Boolean $doEncryption Download Encrypted File
	 * @param  String $encryption_key Key for Document Encryption
	 * @return String File Content
	 */
	public function getDocument($documentId)
	{
        $connection=$this->couchDBConnection();
        $document = $connection->findDocument($documentId);
		return $document;
	}

	public function fetchXmlDocuments(){
	  $obj = new ApplicationTable();
	  $query = "SELECT doc.id 
	    FROM categories_to_documents AS cat_doc
	    JOIN documents AS doc ON doc.imported = 0 AND doc.id = cat_doc.document_id AND doc.mimetype = 'text/xml'
	    WHERE cat_doc.category_id = 1";
	  $result = $obj->zQuery($query);
	  $count  = 0;
	  $module = array();
	  foreach($result as $row) {
	    $content = \Documents\Plugin\Documents::getDocument($row['id']);
	    $module[$count]['doc_id']   = $row['id'];
	    if (preg_match("/<ClinicalDocument/", $content)) {
	      if (preg_match("/2.16.840.1.113883.3.88.11.32.1/", $content)){
		$module[$count]['doc_type'] = 'CCD';
	      }
	      else
		$module[$count]['doc_type'] = 'CCDA';
	    }
	    elseif (preg_match("/<ccr:ContinuityOfCareRecord/", $content))
	      $module[$count]['doc_type'] = 'CCR';
	    $count++;
	  }
	  return $module;
	}

    /* Return document title*/
    public function getLabel($documentId)
    {
        $connection=$this->couchDBConnection();
        $document = $connection->findDocument($documentId);
        $document=$document->body;
        $form_title = $document[self::DOCUMENT][self::LABEL];
        return $form_title;
    }


    /* Return array of fields and the corresponding validator
     * A field without a validator will not be returned
     *
     * example of return value
     *
     *
     *  array (size=1)
     *       'field_name' =>
     *  array (size=2)
     *     0 =>
     *         array (size=1)
     *            'name' => string 'validation class name' (length=11)
     *     1 =>
     *         array (size=1)
     *           'name' => string 'validation class name' (length=11)
     *
     *
     */
    public function getValidationMatrix($documentId)
    {
        $connection=$this->couchDBConnection();
        $document = $connection->findDocument($documentId);
        $document=$document->body;
        $fildesArr=$document[self::DOCUMENT][self::FIELDS];
        $validationMatrix=[];
        foreach ($fildesArr as $fieldName => $content){
            $attributes=$content[SELF::ATTRIBUTES];
            if (array_key_exists(SELF::VALIDATION, $attributes)) {
                $validationMatrix[$fieldName]=$attributes[SELF::VALIDATION];
            }
            if($attributes[self::REQUIRED] == true){
                $validationMatrix[$fieldName][]["name"] = self::REQUIRED;
            }
        }
        return $validationMatrix;
    }


    /* Return array of fields and the corresponding validator
     * A field without a validator will not be returned
     *
     * example of return value
     *
     *
     *  array (size=1)
     *       'field_name' =>
     *  array (size=2)
     *     0 =>
     *         array (size=1)
     *            'name' => string 'validation class name' (length=11)
     *     1 =>
     *         array (size=1)
     *           'name' => string 'validation class name' (length=11)
     *
     *
     */
    public function getValidationMatrixGenericTable($documentId)
    {
        $connection = $this->couchDBConnection();
        $document = $connection->findDocument($documentId);
        $document = $document->body;
        $filedsArr = $document[self::DOCUMENT][self::FIELDS];
        $tableConstraints = $document['table_conditions'];


        $validationMatrix = [];
        foreach ($filedsArr as $fieldName => $content) {
            if (FormhandlerController::str_contains( $fieldName,"_table_generic")) {
                foreach ($tableConstraints as $tableName => $values) {
                    if($tableName == $fieldName) {

                        foreach ($values as $name => $validation) {
                            $validationMatrix[$fieldName][][$name]=$validation;
                        }
                    }
                }
            }

        }
        return $validationMatrix;


    }

    /**
     * saveCouchDocument - Save Document to Couch DB
     * @param Object $connection Couch DB Connection Object
     * @param Json Encoded Data
     * @return Array
     */
    public function saveDraftCouchDocument($data,$id)
    {
        $connection=$this->couchDBConnection();
        $couch 	= $this->getDocument($id);
        $id			= $couch->body['_id'];
        $rev		= $couch->body['_rev'];
        if($id && $rev) {
            $connection->deleteDocument($id,$rev);
            $connection->putDocument($data,$id);
            return $couch;
        } else {
            return false;
        }
    }
} //end

