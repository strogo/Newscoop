/*
 * @(#)VideoLinkFrame.java
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
     * VideoLinkFrame is a frame containing the controls necessary to set the
     * id of video link.
     */
     
import javax.swing.*;
//import java.applet.*;
import java.awt.*;
import java.awt.event.*;
import javax.swing.event.*;
import java.net.*;
import java.util.*;

class VideoLinkFrame extends CampDialog/* implements Runnable*/{
    private String idVal;
    private JComboBox video;
    private VideoLinkProperties vidProps;
    private boolean bIsNew=false;
    private Vector vidPseudos= new Vector();

    public VideoLinkFrame(Campfire p,String title,Vector im, Vector imps){
        super(p, title, 400, 160);

        video=new JComboBox(im);
        vidPseudos= imps;

        addCompo(new JLabel("Video"),video);
        
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
        
        if (video.getItemCount()<1) ok.setEnabled(false);
        
          
    }


	private void okClicked(){
		setVisible(false);
		//dispose();

		vidProps.id=(String)vidPseudos.elementAt(video.getSelectedIndex());
		
		if (bIsNew) {
            CampBroker.getVideoLink().createPresentation(vidProps);
        }else{
            CampBroker.getVideoLink().save(vidProps);
    	}
	}

	private void cancelClicked(){
		setVisible(false);
        //dispose();
	}

	public void open(VideoLinkProperties props, boolean b){
	    int r=0;
	    
	    vidProps=props;
	    bIsNew= b;
	    
	    if (!bIsNew){
    		video.setSelectedIndex(vidPseudos.indexOf(vidProps.id));
	   }
        this.setVisible(true);
		video.requestFocus();
	}

	public void reset(){
	    int r=0;
	    
		video.setSelectedIndex(r);
	}

    
}