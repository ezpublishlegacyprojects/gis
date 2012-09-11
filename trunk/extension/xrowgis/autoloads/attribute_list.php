<?php

class attributeListOperator
{
    /*!
     Constructor
    */
    function attributeListOperator()
    {
        $this->Operators = array( 'list_by_attribute' );
    }

    /*!
     Returns the operators in this class.
    */
    function operatorList()
    {
        return $this->Operators;
    }

    /*!
     \return true to tell the template engine that the parameter list
    exists per operator type, this is needed for operator classes
    that have multiple operators.
    */
    function namedParameterPerOperator()
    {
        return true;
    }

    /*!
     The first operator has two parameters, the other has none.
     See eZTemplateOperator::namedParameterList()
    */
    function namedParameterList()
    {
        return array( 'list_by_attribute' => array(   'attribute' => array( 'type' => 'string',
                                                                     'required' => true,
                                                                     'default' => '' ),
		                                              'limit' => array( 'type' => 'string',
		                                                                     'required' => true,
		                                                                     'default' => '' ),
        											  'source' => array( 'type' => 'string',
	                                                                     'required' => true,
	                                                                     'default' => '' ),
		                                            ) );
		    }

    /*!
     Executes the needed operator(s).
     Checks operator names, and calls the appropriate functions.
    */
    function modify( &$tpl, &$operatorName, &$operatorParameters, &$rootNamespace,
                     &$currentNamespace, &$operatorValue, &$namedParameters )
    {
    	$db = eZDB::instance();
    	$limit = $namedParameters['limit'];
    	$attribute = $namedParameters['attribute'];
    	$source = $namedParameters['source'];
    	
    	$attribute_list = $db->arrayQuery(
    		"SELECT $attribute, count($attribute) as 'count' FROM ezxgis_position, ezcontentobject, ezcontentobject_attribute, ezcontentobject_tree
			WHERE ezxgis_position.contentobject_attribute_id = ezcontentobject_attribute.id
			AND ezcontentobject_attribute.contentobject_id = ezcontentobject.id
			AND ezcontentobject_tree.contentobject_id = ezcontentobject.id
			AND ezcontentobject.current_version = ezxgis_position.contentobject_attribute_version
			AND $attribute != ''
			AND ezcontentobject_tree.path_string LIKE '%/$source/%'
			GROUP BY $attribute
			ORDER BY count desc 
			LIMIT $limit;"
    	);

		$operatorValue = $attribute_list;

    }
}

?>