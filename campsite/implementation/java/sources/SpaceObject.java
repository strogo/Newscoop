/*
 * @(#)SpaceObject.java
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
     * SpaceObject is a object containing all methods concerning spaces
     * found in HTML document
     */
     
import javax.swing.text.*;
import javax.swing.*;
import java.awt.*;
import java.awt.event.*;
import java.util.*;

public final class SpaceObject extends CampHtmlObject {

    private StringBuffer sixb=new StringBuffer();
    private String sixChar;

    public SpaceObject () {
	    sixb.append((char)6);
	    sixChar=new String(sixb);
    }

	public String parseHtml(String f){
	    String s=new String(f);
	    StringBuffer t;
	    int v;
	    int start;

	    while ((v=s.indexOf(sixChar))!=-1)
	    {
	        int nr=0;
	        start=v;
	        while ((v<s.length())&&(s.charAt(v)==(char)6)) 
	            {
	                v++;
	                nr++;
	            }
	        t=new StringBuffer();
	        t.append(s.substring(0,start));
	        t.append(":");
	        t.append(s.substring(start+nr));
	        s=new String(t);
        //System.out.println(" "+start+ " " +nr);
        textPane.setCaretPosition(charPosition(s,start));
        SpaceControl sp=insert();
        sp.value.setText(""+(nr));
	    }
	    return s;
	}
	

   public SpaceControl insert(){
    
        SpaceControl im=new SpaceControl();
        im.value.setDocument(new SpaceFieldDocument(im.value));
        im.value.setText("1");
        insertComponentTo(im);
        objList.addElement(im);
        return im;
   }

}