/*
 * @(#)AudioLinkFrame.java
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
     * AudioLinkFrame is a frame containing the controls necessary to set the
     * id of audio link.
     */
     
import javax.swing.*;
//import java.applet.*;
import java.awt.*;
import java.awt.event.*;
import javax.swing.event.*;
import java.net.*;
import java.util.*;

class AudioLinkFrame extends CampDialog/* implements Runnable*/{
    private String idVal;
    private JComboBox audio;
    private AudioLinkProperties audProps;
    private boolean bIsNew=false;
    private Vector audPseudos= new Vector();

    public AudioLinkFrame(Campfire p,String title,Vector im, Vector imps){
        super(p, title, 400, 160);

        audio=new JComboBox(im);
        audPseudos= imps;

        addCompo(new JLabel("Audio"),audio);
        
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
        
        if (audio.getItemCount()<1) ok.setEnabled(false);
        
          
    }


	private void okClicked(){
		setVisible(false);
		//dispose();

		audProps.id=(String)audPseudos.elementAt(audio.getSelectedIndex());
		
		if (bIsNew) {
            CampBroker.getAudioLink().createPresentation(audProps);
        }else{
            CampBroker.getAudioLink().save(audProps);
    	}
	}

	private void cancelClicked(){
		setVisible(false);
        //dispose();
	}


	public void open(AudioLinkProperties props, boolean b){
	    int r=0;
	    
	    audProps=props;
	    bIsNew= b;
	    
	    if (!bIsNew){
    		audio.setSelectedIndex(audPseudos.indexOf(audProps.id));
	    }
	    
        this.setVisible(true);
		audio.requestFocus();
	}

	public void reset(){
	    int r=0;
	    
		audio.setSelectedIndex(r);
	}

    
}