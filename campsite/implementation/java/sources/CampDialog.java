/*
 * @(#)CampDialog.java
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
     * CampDialog : is the ancestor for other dialogs in the package
     */



import javax.swing.*;
import javax.swing.event.*;
import java.awt.*;
import java.awt.event.*;
import java.util.*;

class CampDialog extends JDialog{
    
    protected Container cp;
    protected JPanel panel=new JPanel();
    protected GridBagLayout gbl;
    protected GridBagConstraints gbc;
    protected static JButton ok, cancel;
    protected static Campfire parent;
    
    protected CampDialog(Campfire p, String title){
        super (p.getParentFrame(), title, true);
        parent=p;
        initDialog( title);
    }

    protected CampDialog(Campfire p, String title, int w, int h){
        super (p.getParentFrame(), title, true);
        parent=p;
        setSize( w, h);
        initDialog( title);

    }
    
    private void initDialog(String title){
        
        //parent= parentApplet;
        //setVisible(false);
        
        ok=new JButton("OK");
        cancel=new JButton("Cancel");
        ok.setPreferredSize(new Dimension(80,26));
        ok.setMaximumSize(new Dimension(80,26));
        cancel.setPreferredSize(new Dimension(80,26));
        cancel.setMaximumSize(new Dimension(80,26));

        cp=getContentPane();
        cp.add(new JScrollPane(panel));

        gbl=new GridBagLayout();
        gbc=new GridBagConstraints();
        //cp.setLayout(gbl);
        panel.setLayout(gbl);
        
        gbc.anchor=GridBagConstraints.NORTHWEST;
        gbc.gridwidth=GridBagConstraints.REMAINDER;
        gbc.insets=new Insets(0,10,10,10);
        gbc.anchor=GridBagConstraints.NORTH;
        gbc.fill=GridBagConstraints.HORIZONTAL;
        centerFrame();
    }

    private void centerFrame() {
         Dimension sdim = Toolkit.getDefaultToolkit().getScreenSize();
         int fw = getSize().width;
         int fh = getSize().height;
         int fx = (sdim.width-fw)/2;
         int fy = (sdim.height-fh)/2;
             
         this.setBounds(fx, fy, fw, fh);
 
    }        
    
    protected void addCompo(JComponent o1,JComponent o2){
        JPanel holder=new JPanel();
        holder.add(o2);
        holder.setLayout(new FlowLayout(FlowLayout.LEFT));
        gbc.anchor=GridBagConstraints.WEST;
        gbc.gridwidth=1;
        gbc.insets=new Insets(3,10,3,10);
        panel.add(o1,gbc);
        panel.add(Box.createHorizontalStrut(10));
        gbc.gridwidth=GridBagConstraints.REMAINDER;
        panel.add(holder,gbc);
    }


    protected void addCompo(JComponent o2){
        JPanel holder=new JPanel();
        holder.add(o2);
        holder.setLayout(new FlowLayout(FlowLayout.CENTER));
//        gbc.anchor=GridBagConstraints.WEST;
//        gbc.gridwidth=1;
//        gbc.insets=new Insets(3,10,3,10);
//        panel.add(gbc);
        panel.add(Box.createHorizontalStrut(10));
        gbc.gridwidth=GridBagConstraints.REMAINDER;
        panel.add(holder,gbc);
    }


    protected void showStatus(String s){
        parent.showStatus(s);
    }

    protected void showError(String s){
        JOptionPane op=new JOptionPane();
        op.showMessageDialog(this,s,"Error",JOptionPane.ERROR_MESSAGE);
    }

    protected void showInfo(String s){
        JOptionPane op=new JOptionPane();
        op.showMessageDialog(this,s,"Info",JOptionPane.INFORMATION_MESSAGE);
    }

}