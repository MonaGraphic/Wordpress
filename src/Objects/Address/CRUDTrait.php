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

namespace Splash\Local\Objects\Address;

use Splash\Core\SplashCore                  as Splash;
use Splash\Local\Objects\Users\CRUDTrait    as UserCRUDTrait;
    
/**
 * @abstract    Wordpress Customer Address CRUD Functions
 */
trait CRUDTrait {
    
    use UserCRUDTrait;
    
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
        // Decode Address User Id
        $UserId = $this->DecodeUserId($Id);
        //====================================================================//
        // Init Object 
        $User       =       get_user_by( "ID" , $UserId );
        if ( is_wp_error($User) )   {
            return Splash::Log()->Err("ErrLocalTpl",__CLASS__,__FUNCTION__," Unable to load User for Address (" . $Id . ").");
        }
        return $User;
    }    
    
    /**
     * @abstract    Create Request Object 
     * 
     * @param       array   $List         Given Object Data
     * 
     * @return      object     New Object
     */
    public function Create()
    {
        //====================================================================//
        // Stack Trace
        Splash::Log()->Trace(__CLASS__,__FUNCTION__); 
        //====================================================================//
        // Not Allowed
        return Splash::Log()->Err("ErrLocalTpl",__CLASS__,__FUNCTION__," Creation of Customer Address Not Allowed.");
    }
        
    /**
     * @abstract    Delete requested Object
     * 
     * @param       int     $Id     Object Id.  If NULL, Object needs to be created.
     * 
     * @return      bool                          
     */    
    public function Delete($Id = NULL)
    {
        //====================================================================//
        // Stack Trace
        Splash::Log()->Trace(__CLASS__,__FUNCTION__);  
        //====================================================================//
        // Not Allowed
        return Splash::Log()->War("ErrLocalTpl",__CLASS__,__FUNCTION__," Delete of Customer Address Not Allowed.");
    }
    
}
