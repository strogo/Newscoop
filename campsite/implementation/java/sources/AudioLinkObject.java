/*
 * @(#)AudioLinkObject.java
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
     * AudioLinkObject is a object containing all methods concerning audio
     * links found in HTML document
     */
     
import javax.swing.text.*;
import javax.swing.*;
import java.awt.*;
import java.awt.event.*;
import java.util.*;

public final class AudioLinkObject extends CampHtmlObject {

    private Vector vectorOfAudios,vectorOfAudioPseudos;
    private AudioLinkFrame myWin;

    public AudioLinkObject () {

    }
    
    public void init(Campfire p,Vector vcOfAudios,Vector vcOfAudioPseudos){
        init(p);
        myWin=null;
        vectorOfAudios=vcOfAudios;
        vectorOfAudioPseudos=vcOfAudioPseudos;
    }

	public String parseHtml(String s){
	    String my=new String(s);
	    StringBuffer t;
	    int fbeg, sbeg, fend, send;
	    AudioLinkProperties audProps;
	    
	    while ((fbeg=firstTag(my,"<!** LINK AUDIO",0))!=-1)
	    {
	        t=new StringBuffer();
	        audProps= new AudioLinkProperties();
	        
	        fend=my.indexOf(">",fbeg);
	        sbeg=my.indexOf("<!** EndLink",fend);
            send=my.indexOf(">",sbeg);
	        
	        t.append(my.substring(0,fbeg));
	        //t.append(":");
	        t.append(my.substring(fend+1, sbeg));
	        //t.append(":");
	        t.append(my.substring(send+1));
	        
	        // find id
	        audProps.id=my.substring(fbeg+16,fend);
    	    audProps.selStart=fbeg;
    	    audProps.selEnd=fbeg+(sbeg-fend)-1;
            textPane.setSelectionStart(audProps.selStart);
            textPane.setSelectionEnd(audProps.selEnd);

            createPresentation(audProps);
	        my=t.toString();
	    }
	    return my;
	}

    public void create(){
	    AudioLinkProperties audProps= new AudioLinkProperties();

        audProps.selStart=textPane.getSelectionStart();
        audProps.selEnd=textPane.getSelectionEnd();

        if (createIsValid()) {
    	    openDialog();
            myWin.open(audProps, true);
        }
    }

	public void edit(Integer i){
	   AudioLinkProperties myProps= new AudioLinkProperties();;
	   
	   myProps= (AudioLinkProperties)objList.get( i.intValue());

	    openDialog();
        myWin.open(myProps, false);
	}

	public void save(AudioLinkProperties props){
        int myIndex= props.objIndex;
        
        objList.set( myIndex, props);

	}

   public void createPresentation(AudioLinkProperties props){

        int ss=props.selStart;
        int se=props.selEnd;
    
        if (ss>se){
            int swap=se;
            se=ss;
            ss=se;
        }
    
        props.objIndex= objIndex;
        objList.addElement(props);
        parent.htmleditorkit.createPresentation("AudioLink", new Integer(objIndex));
        objIndex++;

   }

    private void openDialog(){
        if (myWin==null){
            myWin=new AudioLinkFrame(parent, "Audio Link",vectorOfAudios, vectorOfAudioPseudos);
        }else{
            myWin.reset();
        }
    }

    public String getFirstTag( Integer i){
       String sTag= new String();
	   AudioLinkProperties myProps= new AudioLinkProperties();;
	   
	   myProps= (AudioLinkProperties)objList.get( i.intValue());
	   sTag= "<!** Link audio " + myProps.id + ">";
        
       return sTag;
        
    }


}