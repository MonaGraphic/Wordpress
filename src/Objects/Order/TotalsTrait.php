<?php
/*
 * Copyright (C) 2017   Splash Sync       <contact@splashsync.com>
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
*/

namespace Splash\Local\Objects\Order;

/**
 * @abstract    WooCommerce Order Totals Data Access
 */
trait TotalsTrait {
    
    //====================================================================//
    // Fields Generation Functions
    //====================================================================//

    /**
    *   @abstract     Build Fields using FieldFactory
    */
    private function buildTotalsFields()   {

        //====================================================================//
        // PRICES INFORMATIONS
        //====================================================================//
        
        //====================================================================//
        // Order Total Price HT
        $this->FieldsFactory()->Create(SPL_T_DOUBLE)
                ->Identifier("total_ht")
                ->Name(__("Order total") . " (Tax Excl.)")
                ->MicroData("http://schema.org/Invoice","totalPaymentDue")
                ->ReadOnly();
        
        //====================================================================//
        // Order Total Price TTC
        $this->FieldsFactory()->Create(SPL_T_DOUBLE)
                ->Identifier("total")
                ->Name(__("Order total"))
                ->MicroData("http://schema.org/Invoice","totalPaymentDueTaxIncluded")
                ->isListed()
                ->ReadOnly();   
       
    }    

    //====================================================================//
    // Fields Reading Functions
    //====================================================================//
    
    /**
     *  @abstract     Read requested Field
     * 
     *  @param        string    $Key                    Input List Key
     *  @param        string    $FieldName              Field Identifier / Name
     * 
     *  @return         none
     */
    private function getTotalsFields($Key,$FieldName)
    {
        //====================================================================//
        // READ Fields
        switch ($FieldName)
        {
            case 'total_ht':
                $this->Out[$FieldName] = (double) trim($this->Object->get_total() - $this->Object->get_total_tax());
                break;
            
            case 'total':
                $this->Out[$FieldName] = (double) trim($this->Object->get_total());
                break;
            
            default:
                return;
        }
        
        unset($this->In[$Key]);
    }
        
    //====================================================================//
    // Fields Writting Functions
    //====================================================================//
      
//    /**
//     *  @abstract     Write Given Fields
//     * 
//     *  @param        string    $FieldName              Field Identifier / Name
//     *  @param        mixed     $Data                   Field Data
//     * 
//     *  @return         none
//     */
//    private function setMainFields($FieldName,$Data) 
//    {
//        //====================================================================//
//        // WRITE Field
//        switch ($FieldName)
//        {
//            case '_sku':
//            case '_weight':
//            case '_length':
//            case '_width':
//            case '_height':
//                $this->setPostMeta($FieldName,$Data);
//                break;
//
//            default:
//                return;
//        }
//        
//        unset($this->In[$FieldName]);
//    }
    
}
