/*
 * @(#)CampToolbarIcon.java
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
     * CampToolbarIcon. The icons of the toolbar are stored outside of the jar,
     * so they can be easily changed. But it is time-consuming to get 16 small images,
     * so why don't put all the images into one big image (big.gif).
     * This class maps the image names with "gif" extensions into portions
     * of the big.gif image.
     */



import javax.swing.*;
import java.awt.*;

class CampToolbarIcon extends ImageIcon{

    Image im;
    private int r=0,c=0,w=2,h=2;

    public CampToolbarIcon(String s,Image image,Campfire parent){
        super();
        if (s.equals(CampConstants.TB_ICON_BOLD)) {r=0;c=0;}
        if (s.equals(CampConstants.TB_ICON_ITALIC)) {r=0;c=1;}
        if (s.equals(CampConstants.TB_ICON_UNDERLINE)) {r=0;c=2;}
        if (s.equals(CampConstants.TB_ICON_COLOR)) {r=0;c=3;}
        if (s.equals(CampConstants.TB_ICON_INTLINK)) {r=0;c=4;}

        if (s.equals(CampConstants.TB_ICON_LEFT)) {r=1;c=0;}
        if (s.equals(CampConstants.TB_ICON_CENTER)) {r=1;c=1;}
        if (s.equals(CampConstants.TB_ICON_RIGHT)) {r=1;c=2;}
        if (s.equals(CampConstants.TB_ICON_PREVIEW)) {r=1;c=3;}
        if (s.equals(CampConstants.TB_ICON_REDO)) {r=1;c=4;w=1;}
        
        if (s.equals(CampConstants.TB_ICON_CUT)) {r=2;c=0;}
        if (s.equals(CampConstants.TB_ICON_COPY)) {r=2;c=1;}
        if (s.equals(CampConstants.TB_ICON_PASTE)) {r=2;c=2;}
        if (s.equals(CampConstants.TB_ICON_NEW)) {r=2;c=3;}
        if (s.equals(CampConstants.TB_ICON_UNDO)) {r=2;c=4;w=1;}

        if (s.equals(CampConstants.TB_ICON_CLIP)) {r=3;c=0;}
        if (s.equals(CampConstants.TB_ICON_CLEARALL)) {r=3;c=1;}
        if (s.equals(CampConstants.TB_ICON_IMAGE)) {r=3;c=2;}
        if (s.equals(CampConstants.TB_ICON_TABLE)) {r=5;c=1;}
        if (s.equals(CampConstants.TB_ICON_ADDON)) {r=5;c=4;}
        if (s.equals(CampConstants.TB_ICON_VIDEO)) {r=5;c=3;}
        if (s.equals(CampConstants.TB_ICON_AUDIO)) {r=5;c=2;}
        if (s.equals(CampConstants.TB_ICON_FLASH)) {r=3;c=2;}
        if (s.equals(CampConstants.TB_ICON_JAVA)) {r=3;c=2;}
        if (s.equals(CampConstants.TB_ICON_UPLOAD)) {r=3;c=3;}
        if (s.equals(CampConstants.TB_ICON_SUBHEAD)) {r=3;c=4;}

        if (s.equals(CampConstants.TB_ICON_REGEN)) {r=4;c=0;}
        if (s.equals(CampConstants.TB_ICON_MODEL)) {r=4;c=1;}
        if (s.equals(CampConstants.TB_ICON_HTML)) {r=4;c=2;}
        if (s.equals(CampConstants.TB_ICON_EXTLINK)) {r=4;c=3;}
        if (s.equals(CampConstants.TB_ICON_KEYWORD)) {r=4;c=4;}

        int wi,he;
        int mx=25;
        if (w==2) wi=25; else wi=15;
        if (h==2) he=25; else he=15;
        im=parent.createImage(wi,he);
        im.getGraphics().drawImage(image,0,0,wi,he,c*mx,r*mx,c*mx+wi,r*mx+he,Color.blue,null);
        super.setImage(im);
    }
    
}