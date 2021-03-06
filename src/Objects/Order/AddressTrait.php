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

use Splash\Local\Objects\Address;

/**
 * @abstract    WooCommerce Order Address Fields Access
 */
trait AddressTrait {
    
    /**
    *   @abstract     Build Address Fields using FieldFactory
    */
    private function buildAddressFields()   {
        
        //====================================================================//
        // Billing Address
        $this->FieldsFactory()->Create(self::Objects()->Encode( "Address" , SPL_T_ID))
                ->Identifier("billing_address_id")
                ->Name(__('Billing details'))
                ->MicroData("http://schema.org/Order","billingAddress")
                ->ReadOnly();  
        
        //====================================================================//
        // Shipping Address
        $this->FieldsFactory()->Create(self::Objects()->Encode( "Address" , SPL_T_ID))
                ->Identifier("shipping_address_id")
                ->Name(__('Shipping details'))
                ->MicroData("http://schema.org/Order","orderDelivery")
                ->ReadOnly();  
        
    }    
    

    /**
     *  @abstract     Read requested Field
     * 
     *  @param        string    $Key                    Input List Key
     *  @param        string    $FieldName              Field Identifier / Name
     * 
     *  @return         none
     */
    private function getAddressFields($Key,$FieldName)
    {
        
        //====================================================================//
        // READ Fields
        switch ($FieldName)
        {
            //====================================================================//
            // Billing/Shipping Address Object Id Readings
            case 'billing_address_id':
            case 'shipping_address_id':
                
                $CustomerId = $this->Object->get_customer_id();
                if ( !$CustomerId ) {
                    $this->Out[$FieldName] = Null;
                    break;
                } 
                if ($FieldName == "billing_address_id") {
                    $this->Out[$FieldName] = self::Objects()->Encode( "Address" , Address::EncodeBillingId($CustomerId));
                } else {
                    $this->Out[$FieldName] = self::Objects()->Encode( "Address" , Address::EncodeDeliveryId($CustomerId));
                }
                break;  
                
            default:
                return;
        }
        
        unset($this->In[$Key]);
    }
    
}
