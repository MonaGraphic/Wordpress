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
use Splash\Models\Objects\PricesTrait;
use Splash\Models\Objects\ImagesTrait;
use Splash\Models\Objects\ObjectsTrait;
use Splash\Models\Objects\ListsTrait;

/**
 * @abstract    WooCommerce Product Object
 */
class Product extends AbstractObject
{
    // Splash Php Core Traits
    use IntelParserTrait;
    use SimpleFieldsTrait;    
    use PricesTrait;
    use ImagesTrait;
    use ObjectsTrait;    
    use ListsTrait;
    
    // Core Fields
    use \Splash\Local\Objects\Core\MultilangTrait;              // Multilang Fields Manager  
    use \Splash\Local\Objects\Core\WooCommerceObjectTrait;      // Trigger WooCommerce Module Activation  
    
    // Post Fields
    use \Splash\Local\Objects\Post\CRUDTrait;                   // Objects CRUD
//    use \Splash\Local\Objects\Post\HooksTrait;                  // Wordpress Events
    use \Splash\Local\Objects\Post\MetaTrait;                   // Object MetaData
    use \Splash\Local\Objects\Post\ThumbTrait;                  // Thumbnail Image
    use \Splash\Local\Objects\Post\CustomTrait;                 // Custom Fields
    
    // Products Fields
    use \Splash\Local\Objects\Product\HooksTrait;               // Wordpress Events
    use \Splash\Local\Objects\Product\CoreTrait;                
    use \Splash\Local\Objects\Product\MainTrait;        
    use \Splash\Local\Objects\Product\StockTrait;
    use \Splash\Local\Objects\Product\PriceTrait;
    use \Splash\Local\Objects\Product\ImagesTrait;
    use \Splash\Local\Objects\Product\VariationTrait;
    
    
    //====================================================================//
    // Object Definition Parameters	
    //====================================================================//
    
    /**
     *  Object Disable Flag. Uncomment this line to Override this flag and disable Object.
     */
//    protected static    $DISABLED        =  True;
    
    /**
     *  Object Name (Translated by Module)
     */
    protected static    $NAME            =  "Product";
    
    /**
     *  Object Description (Translated by Module) 
     */
    protected static    $DESCRIPTION     =  "WooCommerce Product Object";    
    
    /**
     *  Object Icon (FontAwesome or Glyph ico tag) 
     */
    protected static    $ICO     =  "fa fa-product-hunt";
    
    /**
     *  Object Synchronization Recommended Configuration 
     */
    protected static    $ENABLE_PUSH_CREATED       =  FALSE;        // Enable Creation Of New Local Objects when Not Existing
        
    //====================================================================//
    // General Class Variables	
    //====================================================================//
    
    var $post_type          = "product";
    var $post_search_type   = array( "product" , "product_variation" );
    
    /**
    *   @abstract     Return List Of Customer with required filters
    *   @param        array   $filter               Filters for Customers List. 
    *   @param        array   $params              Search parameters for result List. 
    *                         $params["max"]       Maximum Number of results 
    *                         $params["offset"]    List Start Offset 
    *                         $params["sortfield"] Field name for sort list (Available fields listed below)    
    *                         $params["sortorder"] List Order Constraign (Default = ASC)    
    *   @return       array   $data             List of all customers main data
    *                         $data["meta"]["total"]     ==> Total Number of results
    *                         $data["meta"]["current"]   ==> Total Number of results
    */
    public function ObjectsList($filter=NULL,$params=NULL)
    {
        //====================================================================//
        // Stack Trace
        Splash::Log()->Trace(__CLASS__,__FUNCTION__);  

        $data       = array();
        $statuses   = get_page_statuses();
        
        //====================================================================//
        // Load From DataBase
        $RawData = get_posts([
            'post_type'         =>      $this->post_search_type,
            'post_status'       =>      array_keys(get_post_statuses()),
            'numberposts'       =>      ( !empty($params["max"])        ? $params["max"] : 10  ),
            'offset'            =>      ( !empty($params["offset"])     ? $params["offset"] : 0  ),
            'orderby'           =>      ( !empty($params["sortfield"])  ? $params["sortfield"] : 'id'  ),
            'order'             =>      ( !empty($params["sortorder"])  ? $params["sortorder"] : 'ASC' ),
            's'                 =>      ( !empty($filter)  ? $filter : '' ),
        ]);
        
        //====================================================================//
        // Store Meta Total & Current values 
        $Totals     =   wp_count_posts('product');
        $data["meta"]["total"]      =   $Totals->publish + $Totals->future + $Totals->draft + $Totals->pending + $Totals->private + $Totals->trash;  
        $VarTotals =   wp_count_posts("product_variation");
        $data["meta"]["total"]     +=   $VarTotals->publish + $VarTotals->future + $VarTotals->draft + $VarTotals->pending + $VarTotals->private + $VarTotals->trash;  
        $data["meta"]["current"]    =   count($RawData);
        
        //====================================================================//
        // For each result, read information and add to $data
        foreach ($RawData as $Product) {
            $data[] = array(
                "id"            =>  $Product->ID,
                "post_title"    =>  $Product->post_title,
                "post_name"     =>  $Product->post_name,
                "post_status"   =>  ( isset($statuses[$Product->post_status]) ? $statuses[$Product->post_status] : "...?" ),
                "_sku"          =>  get_post_meta( $Product->ID, "_sku", True ),
                "_stock"        =>  get_post_meta( $Product->ID, "_stock", True ),
                "_price"        =>  get_post_meta( $Product->ID, "_price", True ),
                "_regular_price"=>  get_post_meta( $Product->ID, "_regular_price", True ),
            );
        }
        
        Splash::Log()->Deb("MsgLocalTpl",__CLASS__,__FUNCTION__, " " . count($RawData) . " Post Found.");
        return $data;
    }
    
    /**
     * @abstract    Load Request Object 
     * 
     * @param       array   $Id               Object id
     * 
     * @return      mixed
     */
    public function Load( $Id )
    {
        //====================================================================//
        // Stack Trace
        Splash::Log()->Trace(__CLASS__,__FUNCTION__);  
        //====================================================================//
        // Init Object 
        $Post           =       get_post( $Id );
        $this->Product  =       wc_get_product( $Id );
        if ( is_wp_error($Post) )   {
            return Splash::Log()->Err("ErrLocalTpl",__CLASS__,__FUNCTION__," Unable to load " . self::$Name . " (" . $Id . ").");
        }
        return $Post;
    }      
    
}
