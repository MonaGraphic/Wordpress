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

namespace Splash\Local\Objects\Core;

/**
 * Wordpress Simple Data Access
 */
trait FieldsTrait {
    
    //====================================================================//
    // Fields Generation Functions
    //====================================================================//

    /**
    *   @abstract     Build Core Fields using FieldFactory
    */
    private function buildCoreFields()   {

        //====================================================================//
        // Title
        $this->FieldsFactory()->Create(SPL_T_VARCHAR)
                ->Identifier("post_name")
                ->Name( __("Slug") )
                ->isLogged()
                ->Description( __("Post") . " : " . __("Permalink") )
                ->MicroData("http://schema.org/Article","identifier")       
//                ->isRequired()
//                ->IsListed();
            ;
        
        //====================================================================//
        // Title
        $this->FieldsFactory()->Create(SPL_T_VARCHAR)
                ->Identifier("post_title")
                ->Name( __("Title") )
                ->isLogged()
                ->Description( __("Post") . " : " . __("Title") )
                ->MicroData("http://schema.org/Article","name")
                ->isRequired()
                ->IsListed();

//        //====================================================================//
//        // Firstname
//        $this->FieldsFactory()->Create(SPL_T_VARCHAR)
//                ->Identifier("firstname")
//                ->Name($langs->trans("Firstname"))
//                ->isLogged()
//                ->MicroData("http://schema.org/Person","familyName")
//                ->Association("firstname","lastname");        
//        
//        //====================================================================//
//        // Lastname
//        $this->FieldsFactory()->Create(SPL_T_VARCHAR)
//                ->Identifier("lastname")
//                ->Name($langs->trans("Lastname"))
//                ->isLogged()
//                ->MicroData("http://schema.org/Person","givenName")
//                ->Association("firstname","lastname");        
//                
//        //====================================================================//
//        // Reference
//        $this->FieldsFactory()->Create(SPL_T_VARCHAR)
//                ->Identifier("code_client")
//                ->Name($langs->trans("CustomerCode"))
//                ->Description($langs->trans("CustomerCodeDesc"))
//                ->IsListed()
//                ->MicroData("http://schema.org/Organization","alternateName");
//        //====================================================================//
//        // Set as Read Only when Auto-Generated by Dolibarr        
//        if ($conf->global->SOCIETE_CODECLIENT_ADDON != "mod_codeproduct_leopard") {
//             $this->FieldsFactory()->ReadOnly();
//        }  
//        
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
    private function getCoreFields($Key,$FieldName)
    {
        
        if ( property_exists($this->Object, $FieldName) ) {
            $this->Out[$FieldName] = $this->Object->$FieldName;
            
            unset($this->In[$Key]);
        } 
        
        return;
        
        //====================================================================//
        // Read Company FullName => Firstname, Lastname - Compagny
        $fullname = $this->decodeFullName($this->Object->name);
        
        //====================================================================//
        // READ Fields
        switch ($FieldName)
        {
            //====================================================================//
            // Fullname Readings
            case 'name':
            case 'firstname':
            case 'lastname':
                $this->Out[$FieldName] = $fullname[$FieldName];
                break;            
            
            //====================================================================//
            // Direct Readings
            case 'code_client':
                $this->getSingleField($FieldName);
                break;
            default:
                return;
        }
        
        unset($this->In[$Key]);
    }
        
    //====================================================================//
    // Fields Writting Functions
    //====================================================================//
      
    /**
     *  @abstract     Write Given Fields
     * 
     *  @param        string    $FieldName              Field Identifier / Name
     *  @param        mixed     $Data                   Field Data
     * 
     *  @return         none
     */
    private function setCoreFields($FieldName,$Data) 
    {
        //====================================================================//
        // WRITE Field
        switch ($FieldName)
        {
            //====================================================================//
            // Fullname Writtings
            case 'name':
            case 'firstname':
            case 'lastname':
                $this->$FieldName = $Data;
                break;

            //====================================================================//
            // Direct Writtings
            case 'code_client':
                if ( $this->Object->$FieldName != $Data ) {
                    $this->Object->$FieldName = $Data;
                    $this->update = True;
                }  
                break;                    
            default:
                return;
        }
        unset($this->In[$FieldName]);
    }
    
}
