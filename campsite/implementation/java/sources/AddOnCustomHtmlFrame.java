/*
 * @(#)AddOnCustomHtmlFrame.java
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
     * AddOnCustomHtmlFrame is a frame containing the controls necessary to set the
     * source of AddOn
     */
     
import javax.swing.*;
import java.awt.*;
import java.awt.event.*;
import javax.swing.event.*;
import java.net.*;
import java.util.*;

class AddOnCustomHtmlFrame extends CampAddOnDialog{
    private JTextArea htmlEdit;
	private CampAddOnControl myControl;
    private CampAddOnProperties myProps;
    private boolean bIsNew=false;

    public AddOnCustomHtmlFrame(Campfire p,String title){
        super(p, title, 460, 360);
        htmlEdit=new JTextArea();

        htmlEdit.setLineWrap(true);
        htmlEdit.setWrapStyleWord(true);
        JScrollPane areaScrollPane = new JScrollPane(htmlEdit);
        areaScrollPane.setVerticalScrollBarPolicy(JScrollPane.VERTICAL_SCROLLBAR_ALWAYS);
        areaScrollPane.setHorizontalScrollBarPolicy(JScrollPane.HORIZONTAL_SCROLLBAR_ALWAYS);
        areaScrollPane.setPreferredSize(new Dimension(250, 250));
//       addCompo(new JLabel("Html"),areaScrollPane);
//        add("Center", htmlEdit);

        //JPanel holder=new JPanel();
//        BoxLayout leftBox = new BoxLayout(panel, BoxLayout.X_AXIS);
//        panel.add(areaScrollPane);
//        panel.setLayout(new FlowLayout(FlowLayout.CENTER));
//        JSplitPane splitPane = new JSplitPane(JSplitPane.HORIZONTAL_SPLIT);
//        panel.add(splitPane);
//        JPanel holder=new JPanel();
//        holder.add(ok);
//        holder.add(cancel);
//        panel.add(holder);
//        panel.setLayout(new FlowLayout(FlowLayout.CENTER));
//        gbc.anchor=GridBagConstraints.WEST;
//        gbc.gridwidth=1;
//        gbc.insets=new Insets(3,10,3,10);
//        panel.add(areaScrollPane,gbc);
        addCompo(new JLabel("Edit Html"), areaScrollPane);
//        addCompo(areaScrollPane);
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
        String myhtml= new String(htmlEdit.getText());
        
        if (myhtml.length()<1){
            showInfo("Please, add some html !");
            htmlEdit.requestFocus();
        }else{
    		setVisible(false);
    		  
    		myProps.htmlText=htmlEdit.getText();
    		myProps.addOnName="CustomHtml";
        		
    		if (bIsNew) {
                AddOnBroker.getCustomHtml().createPresentation(myProps);
            }else{
        	   myControl.setProperties(myProps);
        	}
        }
	}

	private void cancelClicked(){
		setVisible(false);
	}

	public void open(CampAddOnControl i){
		myControl=i;
		
	    bIsNew= false;
		myProps= myControl.getProperties();
		
        htmlEdit.setText(myProps.htmlText);
        
        this.setVisible(true);
		htmlEdit.requestFocus();
	}

	public void open(CampAddOnProperties props){
	    int r=0;
	    
	    myProps=props;
	    bIsNew= true;
	    
        this.setVisible(true);
		htmlEdit.requestFocus();
	}

	public void reset(){
    	htmlEdit.setText("");
	}

    
}