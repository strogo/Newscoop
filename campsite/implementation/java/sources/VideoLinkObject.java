/*
 * @(#)VideoLinkObject.java
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
     * VideoLinkObject is a object containing all methods concerning video
     * links found in HTML document
     */
     
import javax.swing.text.*;
import javax.swing.*;
import java.awt.*;
import java.awt.event.*;
import java.util.*;

public final class VideoLinkObject extends CampHtmlObject {

    private VideoLinkFrame myWin;
    private Vector vectorOfVideos,vectorOfVideoPseudos;

    public VideoLinkObject () {

    }
    
    public void init(Campfire p,Vector vcOfVideos,Vector vcOfVideoPseudos){
        init(p);
        myWin=null;
        vectorOfVideos=vcOfVideos;
        vectorOfVideoPseudos=vcOfVideoPseudos;
    }

	public String parseHtml(String s){
	    String my=new String(s);
	    StringBuffer t;
	    int fbeg, sbeg, fend, send;
	    VideoLinkProperties vidProps;
	    
	    while ((fbeg=firstTag(my,"<!** LINK VIDEO",0))!=-1)
	    {
	        t=new StringBuffer();
	        vidProps= new VideoLinkProperties();
	        
	        fend=my.indexOf(">",fbeg);
	        sbeg=my.indexOf("<!** EndLink",fend);
            send=my.indexOf(">",sbeg);
	        
	        t.append(my.substring(0,fbeg));
	        //t.append(":");
	        t.append(my.substring(fend+1, sbeg));
	        //t.append(":");
	        t.append(my.substring(send+1));
	        
	        // find id
	        vidProps.id=my.substring(fbeg+16,fend);
    	    vidProps.selStart=fbeg;
    	    vidProps.selEnd=fbeg+(sbeg-fend)-1;
            textPane.setSelectionStart(vidProps.selStart);
            textPane.setSelectionEnd(vidProps.selEnd);
            
            createPresentation(vidProps);
	        my=t.toString();
	    }
	    return my;
	}

    public void create(){
	    VideoLinkProperties vidProps= new VideoLinkProperties();

        vidProps.selStart=textPane.getSelectionStart();
        vidProps.selEnd=textPane.getSelectionEnd();

        if (createIsValid()) {
    	    openDialog();
            myWin.open(vidProps, true);
        }
        
    }

	public void edit(Integer i){
	   VideoLinkProperties myProps= new VideoLinkProperties();;
	   
	   myProps= (VideoLinkProperties)objList.get( i.intValue());
	    openDialog();
        myWin.open(myProps, false);
	}

	public void save(VideoLinkProperties props){
        int myIndex= props.objIndex;
        
        objList.set( myIndex, props);

	}

   public void createPresentation(VideoLinkProperties props){

        int ss=props.selStart;
        int se=props.selEnd;
        
        if (ss>se){
            int swap=se;
            se=ss;
            ss=se;
        }
        
        props.objIndex= objIndex;
        objList.addElement(props);
        parent.htmleditorkit.createPresentation("VideoLink", new Integer(objIndex));
        objIndex++;
    
   }

    private void openDialog(){
        
        if (myWin==null){
            myWin=new VideoLinkFrame(parent, "Video Link",vectorOfVideos, vectorOfVideoPseudos);
        }else{
            myWin.reset();
        }
    }

    public String getFirstTag( Integer i){
       String sTag= new String();
	   VideoLinkProperties myProps= new VideoLinkProperties();;
	   
	   myProps= (VideoLinkProperties)objList.get( i.intValue());
	   sTag= "<!** Link video " + myProps.id + ">";
        
       return sTag;
        
    }


}