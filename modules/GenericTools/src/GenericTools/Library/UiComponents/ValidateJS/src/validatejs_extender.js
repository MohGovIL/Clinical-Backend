
function removeRequiredConstraint(elementId, allConstraints) {

    var constraints = typeof constraints === 'undefined' ? constraints : allConstraints;

    if (typeof elementId === 'string') {
        delete constraints[elementId].presence;
    } else {
        for(var i = 0; i <= elementId.length ; i++){
            delete constraints[elementId[i]].presence;
        }
    }

}

