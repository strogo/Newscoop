/*
 * @(#)CampAddOnBroker.java
 *
 * Copyright (c) 2000,2001 Media Development Loan Fund
 *
 * CAMPSITE is a Unicode-enabled multilingual web content                     
 * management system for news publications.                                   
 * CAMPFIRE is a Unicode-enabled java-based near WYSIWYG text editor.         
 * Copyright (C)2000,2001  Media Development Loan Fund                        
 * contact: contact@campware.org - http://www.campware.org                    
 * Campware encourages further development. Please let us know.               
 *                                                                            
 * This program is free software; you can redistribute it and/or              
 * modify it under the terms of the GNU General Public License                
 * as published by the Free Software Foundation; either version 2             
 * of the License, or (at your option) any later version.                     
 *                                                                            
 * This program is distributed in the hope that it will be useful,            
 * but WITHOUT ANY WARRANTY; without even the implied warranty of             
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the               
 * GNU General Public License for more details.                               
 *                                                                            
 * You should have received a copy of the GNU General Public License          
 * along with this program; if not, write to the Free Software                
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


    /**
     * CampAddOnBroker manage all addons used in Campfire
     */
import javax.swing.*;


public final class AddOnBroker
{
    

	/** Use to show list. */
	public static void chooseAddOn(){
	    addOnList.chooseAddOn();
	}

	/** Use to parse. */
	public static String parseHtml(String code){
	   return customHtml.parseHtml(code);
//	   if (name=="CustomHtml") customHtml.parseHtml();
	}

	/** Use to create. */
	public static void createAddOn(String name){
	   if (name.equals("CustomHtml")) customHtml.insert();
	}

	/** Use to edit. */
	public static void editAddOn(String name, CampAddOnControl i){
	   if (name.equals("CustomHtml")) customHtml.edit(i);
	   //if (name=="CustomHtml") customHtml.edit(i);
	}

	/** Use to init */
	public static void init(Campfire p){
		customHtml.init(p);
		addOnList.init(p);
	}

	/** Use to reset */
	public static void reset(){
		customHtml.reset();
	}

	/** Use to manage custom html */
	public static AddOnCustomHtml getCustomHtml(){
		return customHtml;
	}

	/** Use to choose addon */
	public static AddOnList getAddOnList(){
		return addOnList;
	}


// Attributes:
	private static AddOnList addOnList = null;
	private static AddOnCustomHtml customHtml = null;

	static{
		customHtml = new AddOnCustomHtml();
		addOnList = new AddOnList();
	}
}
