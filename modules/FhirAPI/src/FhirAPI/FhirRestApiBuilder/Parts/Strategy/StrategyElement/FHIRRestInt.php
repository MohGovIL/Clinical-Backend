<?php
/**
 * Date: 29/01/20
 * @author  Dror Golan <drorgo@matrix.co.il>
 * This class Fhir RESTFUL INTERFACE
 */

namespace FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement;


interface   FHIRRestInt
{

    //FHIR Restful declaratoins
    public function read();          // Read the current state of the resource
    public function vread();         // Read the state of a specific version of the resource
    public function readOp();       // Read the current state of the resource using operations
    public function update();       // Update an existing resource by its id (or create it if it is new)
    public function patch();         // Update an existing resource by posting a set of changes to it
    public function delete();        // Delete a resource
    public function history();       // Retrieve the change history for a particular resource
    public function create();            // Create a new resource with a server assigned id
    public function search();         // Search the resource type based on some filter criteria
   /* public function addTypeHistory();           // Retrieve the change history for a particular resource type
    public function addWholeCapabilities();     // 	Get a capability statement for the system
    public function addWholeTransactions();     // 	Update, create or delete a set of resources in a single interaction
    public function addWholeHistory();          // 	Retrieve the change history for all resources
    public function addWholeSearch();           //  Search across all resource types based on some filter criteria*/
}
