/*
 * @(#)AddOnBrokerFrame.java
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
     * AddOnBrokerFrame : is the frame in which you can choose the AddOnHas a textfield
     * with code-complition , and a JList with the existing Addons.
     */



import javax.swing.*;
import javax.swing.event.*;
import java.awt.*;
import java.awt.event.*;
import java.util.*;

class AddOnBrokerFrame extends CampDialog{
    
    private JComboBox addon;
    private Vector wordvect;
    private String pwords[];
    private String pobjs[];
   
    
    public AddOnBrokerFrame(Campfire p, String titles,String[] words, String[] objs){
        super(p, titles, 400, 160);
        pwords=words;
        pobjs=objs;
        

        wordvect=new Vector();
        for (int i=0;i<words.length;i++)
            wordvect.addElement(words[i]);
        
        addon=new JComboBox(wordvect);
        addCompo(new JLabel("Add On"),addon);
        addCompo(ok,cancel);
        cancel.addActionListener(new ActionListener(){
            public void actionPerformed(ActionEvent e){
                cancelClicked();
            }
            });
        ok.addActionListener(new ActionListener(){
            public void actionPerformed(ActionEvent e){
                okClicked();
            }
            });

    }
 
 
	private void okClicked(){
	    String addOnObj= new String();
		setVisible(false);
		
        addOnObj=pobjs[addon.getSelectedIndex()];
    		
        AddOnBroker.createAddOn(addOnObj);
	}

	private void cancelClicked(){
		setVisible(false);
	}
    
   
	public void open(){
        
        this.setVisible(true);
		addon.requestFocus();
	}


    
	public void reset(){
	    int r=0;
		addon.setSelectedIndex(r);
	}

   
    
}