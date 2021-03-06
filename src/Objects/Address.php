<?php
/**
 * This file is part of SplashSync Project.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * 
 *  @author    Splash Sync <www.splashsync.com>
 *  @copyright 2015-2017 Splash Sync
 *  @license   GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007
 * 
 **/
                    
namespace   Splash\Local\Objects;

use Splash\Core\SplashCore      as Splash;

use Splash\Models\AbstractObject;
use Splash\Models\Objects\IntelParserTrait;
use Splash\Models\Objects\SimpleFieldsTrait;
use Splash\Models\Objects\ObjectsTrait;

/**
 * @abstract    WooCommerce Customer Address Object
 */
class Address extends AbstractObject
{
    // Splash Php Core Traits
    use IntelParserTrait;
    use SimpleFieldsTrait;
    use ObjectsTrait;    
    
    // Core Fields
    use \Splash\Local\Objects\Core\WooCommerceObjectTrait;      // Trigger WooCommerce Module Activation  
    
    // User Fields
    use \Splash\Local\Objects\Users\HooksTrait;
    
    // Address Traits
    use \Splash\Local\Objects\Address\CRUDTrait;
    use \Splash\Local\Objects\Address\ObjectListTrait;
    use \Splash\Local\Objects\Address\UserTrait;
    use \Splash\Local\Objects\Address\MainTrait;
    
    //====================================================================//
    // Object Definition Parameters	
    //====================================================================//
    
    /**
     *  Object Name (Translated by Module)
     */
    protected static    $NAME            =  "Customer Address";
    
    /**
     *  Object Description (Translated by Module) 
     */
    protected static    $DESCRIPTION     =  "Wordpress Customer Address Object";    
    
    /**
     *  Object Icon (FontAwesome or Glyph ico tag) 
     */
    protected static    $ICO     =  "fa fa-envelope-o";

    /**
     *  Object Synchronization Limitations 
     *  
     *  This Flags are Used by Splash Server to Prevent Unexpected Operations on Remote Server
     */
    protected static    $ALLOW_PUSH_CREATED         =  FALSE;       // Allow Creation Of New Local Objects
    protected static    $ALLOW_PUSH_UPDATED         =  TRUE;        // Allow Update Of Existing Local Objects
    protected static    $ALLOW_PUSH_DELETED         =  FALSE;       // Allow Delete Of Existing Local Objects
    
    /**
     *  Object Synchronization Recommended Configuration 
     */
    protected static    $ENABLE_PUSH_CREATED       =  FALSE;         // Enable Creation Of New Local Objects when Not Existing
    protected static    $ENABLE_PUSH_UPDATED       =  TRUE;          // Enable Update Of Existing Local Objects when Modified Remotly
    protected static    $ENABLE_PUSH_DELETED       =  FALSE;         // Enable Delete Of Existing Local Objects when Deleted Remotly
        
    //====================================================================//
    // General Class Variables	
    //====================================================================//
    
    private static  $Delivery   =   "shipping";
    private static  $Billing    =   "billing";
    
    protected       $AddressType=   Null;
    
    /**
     * @abstract    Decode User Id
     * 
     * @param       string      $Id               Encoded User Address Id
     * 
     * @return      mixed
     */
    protected function DecodeUserId( $Id )
    {
        //====================================================================//
        // Decode Delivery Ids
        if( strpos($Id, static::$Delivery . "-") === 0) {
            $this->AddressType  = static::$Delivery;  
            return substr($Id, strlen(static::$Delivery . "-"));
        }
        //====================================================================//
        // Decode Billing Ids
        if( strpos($Id, static::$Billing . "-") === 0) {
            $this->AddressType  = static::$Billing;  
            return substr($Id, strlen(static::$Billing . "-"));
        }
        return Null;
    } 
    
    /**
     * @abstract    Encode User Delivery Id
     * 
     * @param       string      $Id               Encoded User Address Id
     * 
     * @return      mixed
     */
    public static function EncodeDeliveryId( $Id )
    {
        return static::$Delivery . "-" . $Id;
    }    

    /**
     * @abstract    Encode User Billing Id
     * 
     * @param       string      $Id               Encoded User Address Id
     * 
     * @return      mixed
     */
    public static function EncodeBillingId( $Id )
    {
        return static::$Billing . "-" . $Id;
    }    
    
    /**
     * @abstract    Encode User Address Field Id
     * 
     * @param       string      $Id               Encoded User Address Id
     * 
     * @return      mixed
     */
    protected function EncodeFieldId( $Id , $Mode = Null)
    {
        if ($Mode) {
            return $Mode . "_" . $Id;
        } 
        return $this->AddressType . "_" . $Id;
    }    
    
}




?>
