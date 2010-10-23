<?php
/**
*
* @author Gaetano Giunta
* @version $Id$
* @copyright (C) Gaetano Giunta 2008-2010
* @license code licensed under the GPL License: see README
* @access public
*/
class eZDebugOperators
{
    static $operators = array(
        'eZDebug' => array(
            'debuglvl' => array(
                'type' => 'string',
                'required' => false,
                'default' => 'debug'
            ),
            'label' => array(
                'type' => 'string',
                'required' => false,
                'default' => ''
            )
        ),
        'objDebug' => array(
            'show_values' => array(
                'type' => 'string',
                'required' => false,
                'default' => ''
            ),
            'level' => array(
                'type' => 'int',
                'required' => false,
                'default' => 2
            )
        ),
        'objInspect' => array(
        ),
        'addTimingPoint' => array(
            'label' => array(
                'type' => 'string',
                'required' => true
            )
        ),
        'numQueries' => array(
            'cluster' => array(
                'type' => 'boolean',
                'required' => false,
                'default' => false
            )
        )

    );

    static $inspectcounter = 1;

    /**
     Returns the operators in this class.
    */
    function operatorList()
    {
        return array_keys( self::$operators );
    }

    /*
     @return true to tell the template engine that the parameter list
      exists per operator type, this is needed for operator classes
      that have multiple operators.
    */
    function namedParameterPerOperator()
    {
        return true;
    }

    /**
     @see eZTemplateOperator::namedParameterList()
    */
    function namedParameterList()
    {
        return self::$operators;
    }

    /**
     Executes the needed operator(s).
     Checks operator names, and calls the appropriate functions.
    */
    function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace,
                     $currentNamespace, &$operatorValue, $namedParameters )
    {
        switch ( $operatorName )
        {
            case 'eZDebug':
                $operatorValue = $this->eZdebug( $operatorValue, $namedParameters['debuglvl'], $namedParameters['label'] );
                break;
            case 'objDebug':
                $operatorValue = $this->objdebug( $operatorValue, $namedParameters['show_values'] == 'show', $namedParameters['level'] );
                break;
            case 'objInspect':
                require_once( 'kernel/common/template.php' );
                $tpl = templateInit();
                $tpl->setVariable( 'counter', self::$inspectcounter );
                if ( class_exists( 'ezPOInspector' ) )
                {
                    $tpl->setVariable( 'value', json_encode( ezPOInspector::objInspect( $operatorValue ) ) );
                    $tpl->setVariable( 'error', false );
                }
                else
                {
                    $tpl->setVariable( 'value', null );
                    $tpl->setVariable( 'error', "Cannot insepct value: extension ezpersistentobject_inspector most likely missing" );
                }
                //$tpl->setVariable( 'sdkversion', eZPublishSDK::version() );
                $operatorValue = $tpl->fetch( 'design:ezdebug/objinspect.tpl' );
                self::$inspectcounter++;
                break;
            case 'addTimingPoint':
                eZDebug::addTimingPoint( $namedParameters['label'] );
                $operatorValue = '';
                break;
            case 'numQueries':
                $operatorValue = $this->numqueries( $namedParameters['cluster'] );
        }
    }

    function eZdebug( $msg, $debuglvl, $label='' )
    {
        switch( $debuglvl )
        {
            case 'notice':
                eZDebug::writeNotice( $msg, $label );
                break;
            case 'debug':
                eZDebug::writeDebug( $msg, $label );
                break;
            case 'warning':
                eZDebug::writeWarning( $msg, $label );
                break;
            case 'error':
                eZDebug::writeError( $msg, $label );
                break;
            default:
                eZDebug::writeDebug( "[$debuglvl] " . $msg, $label );
        }
        return '';
    }

    function objdebug( $obj, $showvals=false, $maxdepth=2, $currdepth=0 )
    {
        $out = '';
        $dumper = new eZTemplateAttributeOperator();
        $dumper->displayVariable( $obj, false, $showvals, $maxdepth, $currdepth, $out );
        eZDebug::writeDebug( $out );
        return '';
    }

    function numqueries( $cluster=false )
    {
        $num = -1;
        $type = '';
        if ( $cluster )
        {
            // are we in cluster mode?
            $ini = eZINI::instance( 'file.ini' );
            $handler = $ini->variable( 'ClusteringSettings', 'FileHandler' );
            if ( $handler == 'eZDBFileHandler' || $handler == 'eZDFSFileHandler' )
            {
                $type =  preg_replace( '/^eZDBFileHandler/', '', $ini->variable( 'ClusteringSettings', 'DBBackend' ) );
                $type =  strtolower( preg_replace( '/^Backend$/', '', $type ) );
                $type .= '_cluster_query';
            }
        }
        else
        {
            $ini = eZINI::instance();
            // we cannot use $db->databasename() because we get the same for mysql and mysqli
            $type = preg_replace( '/^ez/', '', $ini->variable( 'DatabaseSettings', 'DatabaseImplementation' ) );
            $type .= '_query';
        }

        // read accumulator
        $debug = eZDebug::instance();
        if ( isset( $debug->TimeAccumulatorList[$type] ) )
        {
            $num = $debug->TimeAccumulatorList[$type]['count'];
        }
        return $num;
    }

}

?>