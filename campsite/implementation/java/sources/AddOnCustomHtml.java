/*
 * @(#)AddOnCustomHtml.java
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
     * AddOnCustomHtml is a object containing all methods concerning custom html
     * found in HTML document
     */
     
import javax.swing.text.*;
import javax.swing.*;
import java.awt.*;
import java.awt.event.*;
import java.util.*;
import java.net.URL;

public final class AddOnCustomHtml extends CampAddOnObject {

  	private AddOnCustomHtmlFrame myframe;

    public AddOnCustomHtml () {
    }

    public void init( Campfire p){
        super.init(p);
        myframe=null;
    }

 	public String parseHtml(String s){
	    String my=new String(s);
	    StringBuffer t;
	    int fbeg, fend, sbeg, send;
	    CampAddOnProperties myProps;
	    
	    if ((fbeg=firstTag(my, "<!** ADDON CUSTOMHTML", 0))!=-1)
	    {
	        t=new StringBuffer();
            myProps= new CampAddOnProperties();
	        fend=my.indexOf(">",fbeg);
	        sbeg=my.indexOf("<!** EndAddOn",fend);
            send=my.indexOf(">",sbeg);
	        
	        t.append(my.substring(0,fbeg));
	        t.append(":");
	        t.append(my.substring(send+1));

    	    myProps.addOnName=my.substring(fbeg+11, fend);
    	    myProps.carPosition=fbeg;
    	    myProps.htmlText=my.substring(fend+1, sbeg);
            createPresentation(myProps);
	        my=t.toString();
	    }
	    return my;
	}
	

	public void insert(){
	    CampAddOnProperties myProps= new CampAddOnProperties();

        myProps.carPosition=textPane.getCaretPosition();
	    openDialog();
		myframe.open(myProps);
	}

	public void edit(CampAddOnControl i){
	    openDialog();
		myframe.open(i);
	}

    

	public void createPresentation(CampAddOnProperties props){

   	    textPane.setCaretPosition(props.carPosition);
	    CampAddOnControl im=insertControl();
	    im.setProperties( props);
	}

   private CampAddOnControl insertControl(){
        CampAddOnControl im=new CampAddOnControl(new CampToolbarIcon(CampConstants.TB_ICON_ADDON,parent.bigim,parent));
        insertComponentTo(im);
        objList.addElement(im);
        return im;
   }
    
    private void openDialog(){
        if (myframe==null){
            myframe=new AddOnCustomHtmlFrame(parent, "Custom Html");
        }else{
            myframe.reset();
      	}
    }
}