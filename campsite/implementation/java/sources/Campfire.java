/*
 * @(#)Campfire.java
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
     * Campfire is the main class.
     */


import java.io.File;
import java.applet.*;
import java.awt.*;
import java.awt.event.*;
import java.util.*;
import java.io.FileReader;
import java.net.URL;
import java.net.*;
import javax.swing.text.*;
import javax.swing.event.*;
import javax.swing.undo.*;
import javax.swing.*;
import javax.swing.border.*;


public class Campfire extends JApplet{
//********************************************************************************
//********************************************************************************
//****                       Main variables                                   ****
//********************************************************************************
//********************************************************************************
    
    JTextPane textPane=new JTextPane();
    private JMenuBar menubar=new JMenuBar();
    private JToolBar toolbar=new JToolBar();
    private JScrollPane scrollPane;
    private Hashtable actionTable = new Hashtable();

    
    private JPanel statusArea = new JPanel();
    private JPanel holderArea = new JPanel();
    private JLabel status = new JLabel("Ready");

    Image bigim;

    private boolean stopping,dialogShow;
    private boolean debugVer=false;
    
    private StyledDocument doc,unDoc;
    
    private DumperFrame dumpFrame;
    JCheckBoxMenuItem isJustified;
    
    private String artindex="";
    private Container contentPane;
    
    private int dot=0,mark=0;
    HtmlEditorKit htmleditorkit;
    int nrofDictionaryWords=0;
    private StringBuffer toHtml;
    private String dictionary[];
    private boolean modified=false;
    
    String linkscript;
	private int nrofFields=8;
	private String retFields[]=new String[nrofFields];
	
    private String[] fieldNames=new String[]{
                            "UserId",
                            "UserKey",
                            "IdPublication",
                            "NrIssue",
                            "NrSection",
                            "NrArticle",
                            "IdLanguage",
                            "Field"   };


    
    private Color backColor=Color.white;
    private Color foreColor=Color.black;
    Color dictColor=Color.red;
    
    
    private MediaTracker tracker;
    
    private String contentString;
    
    private int defaultport=CampConstants.DEFAULT_PORT;
    private int port=defaultport;
    private URL imagepath=null;  
    private boolean firsttime=true;
    private Vector vectorOfImages,vectorOfImagePseudos;
    private Vector vectorOfAudios,vectorOfAudioPseudos;
    private Vector vectorOfVideos,vectorOfVideoPseudos;
	private String scriptString=CampConstants.SCRIPT_PATH;
	
   
    private String[] cutCopyPasteActionNames=new String[]{
        DefaultEditorKit.cutAction,"Cut",CampConstants.TB_ICON_CUT,
        DefaultEditorKit.copyAction,"Copy",CampConstants.TB_ICON_COPY,
        DefaultEditorKit.pasteAction,"Paste",CampConstants.TB_ICON_PASTE,
        "select-all","Select All",CampConstants.TB_ICON_SELALL
    };

    String[] styleActionNames=new String[]{
        "font-bold","Bold",CampConstants.TB_ICON_BOLD,
        "font-italic","Italic",CampConstants.TB_ICON_ITALIC,
        "font-underline","Underline",CampConstants.TB_ICON_UNDERLINE
    };

    private String[] alignActionNames=new String[]{
        "left-justify","Left",CampConstants.TB_ICON_LEFT,
        "center-justify","Center",CampConstants.TB_ICON_CENTER,
        "right-justify","Right",CampConstants.TB_ICON_RIGHT
//        "dump-model","Dump model to System.out","model.gif"
    };

    private boolean newLine=false;
    private String succesfully;
    private boolean upOK=false;
    
    UndoManager undoManager=new UndoManager();
    UndoAction undoAction =new UndoAction(this);
    RedoAction redoAction =new RedoAction(this);
    
    
//********************************************************************************
//********************************************************************************
//****                       Constructor                                      ****
//********************************************************************************
//********************************************************************************

    public Campfire(){
        contentPane=getContentPane();
    }

    
//********************************************************************************
//********************************************************************************
//****                       populate                                         ****
//********************************************************************************
//********************************************************************************

    private void populate(){
        JMenu fileMenu=new JMenu("File"),
              editMenu=new JMenu("Edit"),
              styleMenu=new JMenu("Font Style"),
              alignMenu=new JMenu("Align"),
              insertMenu=new JMenu("Insert"),
              formatMenu=new JMenu("Format"),
              createMenu=new JMenu("Create"),
              //toolsMenu=new JMenu("Tools"),
              helpMenu=new JMenu("Help");
        
        //toolbar.addSeparator();
        addCommand(new CustomAction("New",CustomAction.NEWFILE,this),fileMenu,CampConstants.TB_ICON_NEW,"New");
        //addCommand(new CustomAction("Revert",CustomAction.RE,this),fileMenu,CampConstants.TB_ICON_REGEN,"Revert to starting version");
        addCommand(new CustomAction("Save",CustomAction.UPLOAD,this),fileMenu,CampConstants.TB_ICON_UPLOAD,"Save");
        fileMenu.addSeparator();
        toolbar.addSeparator();
        addCommand(new CustomAction("Article Preview",CustomAction.PREVIEW,this),fileMenu,CampConstants.TB_ICON_PREVIEW,"Article Preview");
        //fileMenu.addSeparator();
        //fileMenu.add(new CustomAction("Exit",CustomAction.EXIT,this));
        if (debugVer) {
	        toolbar.addSeparator();
	        addCommand(new CustomAction("to System.out",CustomAction.DUMP,this),fileMenu,CampConstants.TB_ICON_DUMP,"Dump to editbox");
	        addCommand(new CustomAction("regenerate Html",CustomAction.SETHTML,this),fileMenu,CampConstants.TB_ICON_HTML,"Regenerate HTML");
        }
        toolbar.addSeparator();
		
        editMenu.add(undoAction);
        editMenu.add(redoAction);
        editMenu.addSeparator();

       	addCommand(getAction(DefaultEditorKit.cutAction),editMenu,"Cut",CampConstants.TB_ICON_CUT,"Cut");
       	addCommand(getAction(DefaultEditorKit.copyAction),editMenu,"Copy",CampConstants.TB_ICON_COPY,"Copy");
       	addCommand(getAction(DefaultEditorKit.pasteAction),editMenu,"Paste",CampConstants.TB_ICON_PASTE,"Paste");

        editMenu.addSeparator();
       	addCommand(getAction("select-all"),editMenu,"Select All");

        formatMenu.add(new FontSizeStyleAction("Font Size 1",textPane,"1"));
        formatMenu.add(new FontSizeStyleAction("Font Size 2",textPane,"2"));
        formatMenu.add(new FontSizeStyleAction("Font Size 3",textPane,"3"));
        formatMenu.add(new FontSizeStyleAction("Font Size 4",textPane,"4"));
        formatMenu.add(new FontSizeStyleAction("Font Size 5",textPane,"5"));
        formatMenu.add(new FontSizeStyleAction("Font Size 6",textPane,"6"));
        formatMenu.add(new FontSizeStyleAction("Font Size 7",textPane,"7"));
        formatMenu.addSeparator();

        toolbar.addSeparator();
        
        for(int i=0; i<styleActionNames.length; ++i) {
            Action action=getAction(styleActionNames[i]);
            if (action!=null) {

                JButton button=toolbar.add(action);
                JMenuItem item=styleMenu.add(action);
                item.setText(styleActionNames[++i]);
                
                button.setText(null);
                button.setToolTipText(styleActionNames[i]);
                button.setIcon(new CampToolbarIcon(styleActionNames[++i],bigim,this));
                //button.setIcon(new ImageIcon(buildURL(imagepath+styleActionNames[++i])));
                button.setRequestFocusEnabled(false);
                button.setMargin(new Insets(1,1,1,1));
                
            }
        }

        formatMenu.add( styleMenu);				
        addCommand(new CustomAction("Font Color",CustomAction.COLOR,this),formatMenu,CampConstants.TB_ICON_COLOR,"Font Color Chooser");
        toolbar.addSeparator();

        int alignCount=alignActionNames.length;
        for(int i=0; i<alignCount; ++i) {
            Action action=getAction(alignActionNames[i]);
            if (action!=null) {
                JButton button=toolbar.add(action);
                JMenuItem item=alignMenu.add(action);
                item.setText(alignActionNames[++i]);
                
                button.setText(null);
                button.setToolTipText(alignActionNames[i]);
                button.setIcon(new CampToolbarIcon(alignActionNames[++i],bigim,this));
                //button.setIcon(new ImageIcon(buildURL(imagepath+alignActionNames[++i])));
                button.setRequestFocusEnabled(false);
                button.setMargin(new Insets(1,1,1,1));
            }
        
        }
        
        toolbar.addSeparator();
        
        formatMenu.addSeparator();
        formatMenu.add( alignMenu);				
        isJustified=new JCheckBoxMenuItem("Justify All");
        formatMenu.add(isJustified);
        formatMenu.addSeparator();
        formatMenu.add(new CustomAction("Clear All Attributes",CustomAction.CLEAR,this));
        
        addCommand(new CustomAction("Image",CustomAction.IMAGE,this),insertMenu,CampConstants.TB_ICON_IMAGE,"Insert Image");
        //addCommand(new CustomAction("Table",CustomAction.TABLE,this),insertMenu,CampConstants.TB_ICON_TABLE,"Insert Table");
        //insertMenu.addSeparator();
        //addCommand(new CustomAction("Add-On",CustomAction.ADDON,this),insertMenu,CampConstants.TB_ICON_ADDON,"Insert Add-On");
        toolbar.addSeparator();
        
        addCommand(new CustomAction("Subhead",CustomAction.SUBHEAD,this),createMenu,CampConstants.TB_ICON_SUBHEAD,"Create Subhead");
        createMenu.addSeparator();
        addCommand(new CustomAction("Keyword Link",CustomAction.WORD,this),createMenu,CampConstants.TB_ICON_KEYWORD,"Create Keyword Link");
        addCommand(new CustomAction("Internal Link",CustomAction.INTLINK,this),createMenu,CampConstants.TB_ICON_INTLINK,"Create Internal Link");
        //addCommand(new CustomAction("Audio Link",CustomAction.AUDIO,this),createMenu,CampConstants.TB_ICON_AUDIO,"Create Audio Link");
        //addCommand(new CustomAction("Video Link",CustomAction.VIDEO,this),createMenu,CampConstants.TB_ICON_VIDEO,"Create Video Link");
        addCommand(new CustomAction("External Link",CustomAction.EXTLINK,this),createMenu,CampConstants.TB_ICON_EXTLINK,"Create External Link");

        //toolsMenu.addSeparator();
        //toolsMenu.add(new CustomAction("Manage Images",CustomAction.UPIMAGE,this));
        //adminMenu.add(new CustomAction("Add Keyword",CustomAction.UPKEYWORD,this));
        //adminMenu.add(new CustomAction("Add Audio Link",CustomAction.UPAUDIO,this));
        //adminMenu.add(new CustomAction("Add Video Link",CustomAction.UPVIDEO,this));
        //toolsMenu.add( adminMenu);				

//        helpMenu.add(new CustomAction("Help Topics",CustomAction.HELP,this));
        helpMenu.add(new CustomAction("Our Home Page",CustomAction.HOMEPAGE,this));
        helpMenu.add(new CustomAction("Bugs Report",CustomAction.BUGS,this));
        helpMenu.addSeparator();
        helpMenu.add(new CustomAction("About",CustomAction.ABOUT,this));
        
        menubar.add(fileMenu);
        menubar.add(editMenu);
        menubar.add(insertMenu);
        menubar.add(formatMenu);
        menubar.add(createMenu);
        //menubar.add(toolsMenu);
        menubar.add(helpMenu);
        
    }

    
//********************************************************************************
//********************************************************************************
//****                       load action table                                ****
//********************************************************************************
//********************************************************************************
    private void loadActionTable(){
        Action[] actions =textPane.getActions();
        
        for(int i=0;i<actions.length;++i) {
            actionTable.put(actions[i].getValue(Action.NAME),actions[i]);
//            out(actions[i].getValue(Action.NAME));
        }
    }
    
//********************************************************************************
//********************************************************************************
//****                       get Action                                       ****
//********************************************************************************
//********************************************************************************
    public Action getAction(String name) {
        return (Action)actionTable.get(name);
    }
    
//********************************************************************************
//********************************************************************************
//****                       Init                                             ****
//********************************************************************************
//********************************************************************************
    public void init(String args[]){

//        out("initializing....");

//      try {
//          UIManager.setLookAndFeel(UIManager.getCrossPlatformLookAndFeelClassName());
//          }
//      catch (Exception exc) {
//          System.err.println("Error loading L&F: " + exc);
//          }
    }
    
    
//********************************************************************************
//********************************************************************************
//****                       start                                            ****
//********************************************************************************
//********************************************************************************
    public void start(){

        
        boolean stopping=false;
        boolean dialogShow=false;
        boolean modified=false;    
      
        if (firsttime) {


            try {
               UIManager.setLookAndFeel(UIManager.getSystemLookAndFeelClassName());
               //UIManager.setLookAndFeel(UIManager.getCrossPlatformLookAndFeelClassName());
               }catch(Exception e) {
                e.printStackTrace();
            }

            if (getParameter(CampConstants.PARAM_DEBUG)!=null) debugVer=true; else debugVer=false;
//            if (getParameter(CampConstants.PARAM_CLIP)!=null) clipVer=true; else clipVer=false;
            artindex=getParameter(CampConstants.PARAM_IDX);
            linkscript=getParameter(CampConstants.PARAM_LINKSCRIPT);    
            String po=getParameter(CampConstants.PARAM_PORT);
    
            if (po==null) port=defaultport;
                else
                {
                    try{
                        port=new Integer(po).intValue();
                    }
                    catch(Exception e){
                        port=defaultport;
                    }
                    if (port==0) port=defaultport;
                }
                
            try{
                imagepath=new URL(getCodeBase(),CampConstants.IMAGE_PATH);
            }
            catch(Exception e){}
            
            tracker=null;
            tracker=new MediaTracker(this);
            
            try{
                URL imurl=buildURL(CampConstants.TB_ICONS);
                bigim=null;
                bigim=fetchImage(imurl,0);
            }
    	    catch (Exception e) {
                out("Error getting image:"+e);
            }
    
    
            scrollPane=new JScrollPane(textPane);
            //textPane.setBackground(backColor);
            //textPane.setForeground(foreColor);
    
            contentPane.add(toolbar, BorderLayout.NORTH);
            contentPane.add(scrollPane,BorderLayout.CENTER);
            contentPane.add(holderArea,BorderLayout.SOUTH);
            holderArea.setLayout(new BorderLayout());
            holderArea.add(statusArea,BorderLayout.SOUTH);
            statusArea.setLayout(new BorderLayout());
            statusArea.add(status,BorderLayout.CENTER);
//            statusArea.setBorder(BorderFactory.createEtchedBorder(EtchedBorder.LOWERED));    
            
            readDictionary();
            readImages();
            readAudios();
            readVideos();
            readColors();
        	readFields();
    
            htmleditorkit=new HtmlEditorKit(this);
            textPane.setEditorKit(htmleditorkit);
            htmleditorkit.install(textPane);
            textPane.setSelectionColor(Color.blue.darker());
            
            loadActionTable();
            populate();
            setJMenuBar(menubar);
//            menubar.setBorder(BorderFactory.createBevelBorder(BevelBorder.RAISED));
//            toolbar.setBorder(BorderFactory.createBevelBorder(BevelBorder.RAISED));
            
            unDoc=textPane.getStyledDocument();
            unDoc.addUndoableEditListener(new UndoableEditListener(){
                public void undoableEditHappened(UndoableEditEvent e){
                    undoManager.addEdit(e.getEdit());
                    undoAction.update();
                    redoAction.update();
                }
                });
    
                
            textPane.getDocument().addDocumentListener(new DocumentListener(){
    
                public void insertUpdate(DocumentEvent e){
                   //out("insert update");
                    setModified();
                    }
                public void changedUpdate(DocumentEvent e){
                    //out("change update");
                    setModified();
                    }
                public void removeUpdate(DocumentEvent e){
                    //out("remove update");
                    setModified();
                    }
                });
                
            CampBroker.getImage().init(this,imagepath,vectorOfImages,vectorOfImagePseudos);
            CampBroker.getInternalLink().init(this);
            CampBroker.getExternalLink().init(this);
            CampBroker.getAudioLink().init(this,vectorOfAudios,vectorOfAudioPseudos);
            CampBroker.getVideoLink().init(this,vectorOfVideos,vectorOfVideoPseudos);
            CampBroker.getKeyword().init(this, dictionary);
            CampBroker.getSubhead().init(this);
            CampBroker.getSpace().init(this);
            CampBroker.getFont().init(this,imagepath);
            AddOnBroker.init(this);

            firsttime=false;

            SwingUtilities.updateComponentTreeUI(getParentFrame());
             
            if ((contentString!=null)&&(contentString.length()!=0))
//                try{
                //textPane.getDocument().insertString(0,"You must press the Revert button to start editing !",null);
//                textPane.insertComponent(new Starter(this));
//                CustomAction b=new CustomAction("",CustomAction.CENTER,this);
//                b.actionPerformed(new ActionEvent(textPane,0,""));
//                }
//                catch(Exception e){
//                }


                try{
              	    regen();
                }
                catch(Exception e){
                }

    
            //textPane.setEnabled(true);
            textPane.requestFocus();
        }       
    }
    
    private void setModified(){
        modified=true;
    }

//********************************************************************************
//********************************************************************************
//****                       read Dictionary and colors                       ****
//********************************************************************************
//********************************************************************************
    private void readDictionary(){
        int ord=0;
        String s;
        final String dict="tol#";
        while((s=getParameter(dict+ord))!=null){
            ord++;
        }
        nrofDictionaryWords=ord;
        ord=0;
        dictionary=new String[nrofDictionaryWords];
        while((s=getParameter(dict+ord))!=null){
            dictionary[ord]=new String(s);
            ord++;
        }
    }
    
    private void readColors(){
        String s;
        if ((s=getParameter(CampConstants.PARAM_BACKGROUND))!=null)
        {
        ColorConverter a=new ColorConverter(s);
        backColor=a.getColor();
        }
        /*if ((s=getParameter(CampConstants.PARAM_FOREGROUND))!=null)
        {
        ColorConverter a=new ColorConverter(s);
        foreColor=a.getColor();
        }*/
        if ((s=getParameter(CampConstants.PARAM_WORDCOLOR))!=null)
        {
        ColorConverter a=new ColorConverter(s);
        dictColor=a.getColor();
        }
    }
    

    
//********************************************************************************
//********************************************************************************
//****                       upload                                           ****
//********************************************************************************
//********************************************************************************
    
    
    public void upload(){

        toHtml=new StringBuffer("");
        doc=textPane.getStyledDocument();
        HtmlGenerator hg=new HtmlGenerator(doc,this,false);
        toHtml= hg.generate();

        showStatus("Saving...");
		Communicator comm=new Communicator(this,port);
		
		if (comm.connect())	{
    		StringBuffer header=new StringBuffer();
    		header.append("POST "+scriptString+" HTTP/1.0\r\n");
    		header.append("Host: "+getCodeBase().getHost()+":"+port+"\r\n");
    		header.append("Pragma: no-cache\r\n");
    		header.append("Cache-control: no-cache\r\n");
    		header.append("User-Agent: TOLCommunicator\r\n");
    		header.append("Content-type: application/x-www-form-urlencoded\r\n");
    
        	StringBuffer fields=new StringBuffer();
        	int len=0;
        
        	for (int k=0;k<nrofFields;k++){
        		StringBuffer tempfields=new StringBuffer();
        		tempfields.append(fieldNames[k]);
        		tempfields.append("=");
        		tempfields.append(new String(URLEncoder.encode(retFields[k])));
        		String returnal=new String(tempfields);
        		fields.append(returnal+"&");
        		len+=(returnal.length()+1);
        	}
        
    		StringBuffer body=new StringBuffer();
    		body.append("Content=");
    		body.append(URLEncoder.encode(new String(toHtml)));
    		String bodyString=new String(body);
    
    		len+=bodyString.length();
    		header.append("Content-length: "+len);
		
    		comm.write(header);
    		comm.write("\r\n\r\n");
    		comm.write(new String(fields)+bodyString);
    		
    		succesfully=comm.read();
    		comm.close();
    		out(succesfully);
    		if (comm.okTrans) upOK=true; else upOK=false;
    		
    		if (stopping) dialogShow=true;
    		if (upOK) {
    		    showStatus("Article field saved");
    		    modified=false;
    		}else{
    		  if (!comm.locked)
    		      showError("Upload unsuccesful!");
    		  else{
    		    stopping=true;
    		    dialogShow=true;
    		    showInfo("You can't save because the article is locked !");
    		    stopping=false;
    		    dialogShow=false;
    		  }
    		}
    	}else{
		    if (stopping) dialogShow=true;
		    showError("Can't connect to server!");
    	}
		    
   }
    
    public void dump(){
       /* out("Dump");
        for (int y=1;y<3;y++)
        System.out.println();*/
 /*       for (int i=0;i<iform.fields.size();i++)
            out(iform.getValue(i));*/
        /*for (int y=1;y<3;y++)
        System.out.println();*/
        toHtml=new StringBuffer("");
        doc=textPane.getStyledDocument();
        
        HtmlGenerator hg=new HtmlGenerator(doc,this,false);
        toHtml=hg.generate();
		//out(new String(toHtml));
        if (dumpFrame==null)
            dumpFrame=new DumperFrame(this);
        else
		    dumpFrame.setVisible(true);
		
		//dumpFrame.setText(new String(toHtml));
		dumpFrame.t.append("\n");
		dumpFrame.t.append(new String(toHtml));
   }


    public void debug(String s){

        if (dumpFrame==null){
            dumpFrame=new DumperFrame(this);
		    dumpFrame.setVisible(true);
		  }
        else
		    dumpFrame.setVisible(true);
		
		dumpFrame.t.append("\n");
		dumpFrame.t.append(new String(s));
   }

   
    private void readImages(){
        int ord=0;
        String s;
        final String im="image";
        vectorOfImages=new Vector();
        vectorOfImagePseudos=new Vector();
        while((s=getParameter(im+ord))!=null){
            vectorOfImagePseudos.addElement(s.substring(0,s.indexOf(",")));
            vectorOfImages.addElement(s.substring(s.indexOf(",")+1));
            ord++;
        }
    }
    
    private void readAudios(){
        int ord=0;
        String s;
        final String im="aud";
        vectorOfAudios=new Vector();
        vectorOfAudioPseudos=new Vector();
        while((s=getParameter(im+ord))!=null){
            vectorOfAudioPseudos.addElement(s.substring(0,s.indexOf(",")));
            vectorOfAudios.addElement(s.substring(s.indexOf(",")+1));
            ord++;
        }
    }

    private void readVideos(){
        int ord=0;
        String s;
        final String im="vid";
        vectorOfVideos=new Vector();
        vectorOfVideoPseudos=new Vector();
        while((s=getParameter(im+ord))!=null){
            vectorOfVideoPseudos.addElement(s.substring(0,s.indexOf(",")));
            vectorOfVideos.addElement(s.substring(s.indexOf(",")+1));
            ord++;
        }
    }

    public void newFile(boolean ask){
        if (ask)
        {
            JOptionPane op=new JOptionPane();
            int selV=op.showConfirmDialog(this,"Do you really want to create a new document by deleting all the content?","New document",JOptionPane.YES_NO_OPTION);
            if (selV==JOptionPane.NO_OPTION) return;
        }    
        textPane.setText("");
        isJustified.setState(false);

        CampBroker.getSubhead().reset();
        CampBroker.getImage().reset();
        CampBroker.getSpace().reset();
        CampBroker.getInternalLink().reset();
        CampBroker.getExternalLink().reset();
        CampBroker.getAudioLink().reset();
        CampBroker.getVideoLink().reset();
        CampBroker.getFont().reset();
        CampBroker.getKeyword().reset();
        AddOnBroker.reset();

    }

    private URL buildURL(String a)
    {
        URL Im=null;
            try{
                Im=new URL(imagepath,a);
            }
            catch(Exception e){}
        return Im;    
    }
    
    public void updateDots(int d, int m){
        mark=m;
        dot=d;
    }
    
    public void insertT(String s){
        try
        {
            int dmm=dot-mark;
            if (dmm<0)
            {
                dmm=-dmm;
                int sw=dot;
                dot=mark;
                mark=sw;
            }
        textPane.getDocument().remove(mark,dmm);
        textPane.getDocument().insertString(dot,s,null);
        }
        catch(Exception e)
        {
            out("ex: "+e);
        }
    }

	private void readFields(){

    	String d;
    	if ((d=getParameter(CampConstants.PARAM_SCRIPT))!=null) scriptString=d;
    	String s=new String();
    	for (int i=0;i<nrofFields;i++)
    	{
        	s=getParameter(fieldNames[i]);
        	if (s==null) retFields[i]=new String(""); else retFields[i]=new String(s);
    	}
    	contentString=getParameter(CampConstants.PARAM_CONTENT);
	}
	
	public void setHtml(){
	    
	    StringBuffer fromHtml=new StringBuffer();
        HtmlGenerator hg=new HtmlGenerator(textPane.getStyledDocument(),this,false);
        fromHtml=hg.generate();
        
	    HtmlParser pars=new HtmlParser(textPane,this,new String(fromHtml));
	    pars.parseHtml();
        
	}

	
    private Image fetchImage(URL imageURL, int trackerClass)
    throws InterruptedException {
        out("loading : "+imageURL.toExternalForm());
        Image image = getImage(imageURL);
        tracker.addImage(image, trackerClass);
        tracker.waitForID(trackerClass);
        return image;
      }
    
    public void stop(){
        stopping=true;
        if (modified)
        {
        JOptionPane op=new JOptionPane();
        
        int selV=op.showConfirmDialog(this,"Do you want to upload before leaving the editor?","Campfire - "+retFields[7],JOptionPane.YES_NO_OPTION);
        if (selV==JOptionPane.NO_OPTION) return;
        upload();
        stopping=false;
        dialogShow=false;
        }
    }

	
	public void regen(){
    
        contentString=new URLDecoder().decode(contentString);
        HtmlParser localParser=new HtmlParser(textPane,this,contentString);
        if (contentString!=null){
            //textPane.setEnabled(false);
            localParser.parseHtml(); 
            //textPane.setEnabled(true);
        }
        modified=false;
	}


//    public void destroy(){
      //JOptionPane.showMessageDialog (null,"You are exiting the Application.... Bye","Bye", JOptionPane.WARNING_MESSAGE);
      //super.destroy();
    //}

//********************************************************************************
//********************************************************************************
//****                       Menu related                                     ****
//********************************************************************************
//********************************************************************************

    private void addCommand(Action a,JMenu menu,String img,String tt){
        JButton button=toolbar.add(a);
        JMenuItem menuitem=menu.add(a);
        button.setText(null);
        button.setIcon(new CampToolbarIcon(img,bigim,this));
        button.setRequestFocusEnabled(false);
        button.setMargin(new Insets(1,1,1,1));
        button.setToolTipText(tt);

    }
    
    private void addCommand(Action a,JMenu menu,String it, String img,String tt){
        JButton button=toolbar.add(a);
        JMenuItem menuitem=menu.add(a);
        menuitem.setText(it);
        button.setText(null);
        button.setIcon(new CampToolbarIcon(img,bigim,this));
        button.setRequestFocusEnabled(false);
        button.setMargin(new Insets(1,1,1,1));
        button.setToolTipText(tt);

    }

    private void addCommand(Action a,JMenu menu,String it){
        JMenuItem menuitem=menu.add(a);
        menuitem.setText(it);

    }

    private void addToolbarCommand(Action a,String img,String tt){
        JButton button=toolbar.add(a);
        button.setText(null);
        button.setIcon(new CampToolbarIcon(img,bigim,this));
        button.setRequestFocusEnabled(false);
        button.setMargin(new Insets(1,1,1,1));
        button.setToolTipText(tt);
    }
    
    
//********************************************************************************
//********************************************************************************
//****                       show status                                      ****
//********************************************************************************
//********************************************************************************
    public void showStatus(String s){
        status.setText(s);
        status.revalidate();
    }

    public void showError(String s){
        JOptionPane op=new JOptionPane();
        op.showMessageDialog(null,s,"Error",JOptionPane.ERROR_MESSAGE);
    }

    public void showInfo(String s){
        JOptionPane op=new JOptionPane();
        op.showMessageDialog(null,s,"Info",JOptionPane.INFORMATION_MESSAGE);
    }
    
    private void out(String s){
        System.out.println(s);
        //showStatus(s);
    }

    private Frame findParentFrame(){ 
        Container c = this; 
        while(c != null){ 
          if (c instanceof Frame) 
            return (Frame)c; 
    
          c = c.getParent(); 
        } 
        return (Frame)null; 
      } 

    public Frame getParentFrame(){ 
        return findParentFrame(); 
      } 


   public void about() {
        JOptionPane op=new JOptionPane();
        String s= new String("CAMPFIRE 2.0 Beta 2, Copyright © 1999-2002 MDLF");
        s= s + "\n" + new String("Maintained and distributed under GNU GPL by CAMPWARE");
        op.showMessageDialog(this,s,"About",JOptionPane.INFORMATION_MESSAGE);
   }

   public void preview() {
        URL userUrl;
        String s= new String("/priv/pub/issues/sections/articles/preview.php");
        String returnal= new String("?");

        returnal= returnal + "Pub=" +  new String(URLEncoder.encode(retFields[2]));   
        returnal= returnal + "&" + "Issue=" +  new String(URLEncoder.encode(retFields[3]));   
        returnal= returnal + "&" + "Section=" +  new String(URLEncoder.encode(retFields[4]));   
        returnal= returnal + "&" + "Article=" +  new String(URLEncoder.encode(retFields[5]));   
        returnal= returnal + "&" + "Language=" +  new String(URLEncoder.encode(retFields[6]));   
        returnal= returnal + "&" + "sLanguage=" +  new String(URLEncoder.encode(retFields[6]));   

/*    	for (int k=0;k<nrofFields;k++)
    	{
    	    if (k==0)
        		returnal= returnal + "?";
        	else
        		returnal= returnal + "&";
        		
    		StringBuffer tempfields=new StringBuffer();
    		tempfields.append(fieldNames[k]);
    		tempfields.append("=");
    		tempfields.append(new String(URLEncoder.encode(retFields[k])));
    		returnal= returnal + new String(tempfields);
    	}
*/

        s= s + returnal;
        try{
            userUrl = new URL(getCodeBase(),s); 
            this.getAppletContext().showDocument(userUrl,"_blank");             
        } catch (Exception exc){
            out("Not valid URL");
        }
   }


   public void exitapp() {
        URL userUrl;
        try{
            userUrl = new URL("javascript:window.close()"); 
            this.getAppletContext().showDocument(userUrl);             
        } catch (Exception exc){
            out("Not valid URL");
        }
   }

}