/*
 * CardViewer.java
 *
 * © <your company here>, 2003-2005
 * Confidential and proprietary.
 */

package com.CAF.card.viewer;

import java.io.*;
import java.util.*;
import java.lang.*;

import javax.microedition.io.*;

// CAF API
import com.CAF.card.admin.*;

// Home Island API
import com.ekendotech.homeIsland.gui.*;

// Blackberry API
import net.rim.device.api.ui.component.*;
import net.rim.device.api.ui.container.*;
import net.rim.device.api.ui.*;
import net.rim.device.api.system.Bitmap;
import net.rim.device.api.collection.*;

/**
 * 
 */
public class CardViewer extends com.ekendotech.homeIsland.gui.IslandBasicGui
{
    private CardViewerApplicationThread appThread;
    private VerticalFieldManager viewer;
    private ReadableList cards;
    private CardAdminWizard daWiz;
    
    public CardViewer() 
    {    
        // set push screen to false
        super(false, true);
        
        // Add Card Viewer to menu
        //this.AddMainMenuItem("View Existing Flash Cards","ApplicationThread");
        
        // Add Admin Wizard to Menu
        //this.AddMainMenuItem("Create New Flash Cards", "ApplicationThread");
        
        // You need to initialize before setting
        appThread = new CardViewerApplicationThread();
        
        // Add Application Handler
        this.SetIslandApplicationThread(appThread);
        
        // create viewer we are going to default - Start
        viewer = this.createViewerGui(true);
        
        // set our Gui manager of choice
        this.SetComponentManager(viewer);
        
        //GuiAssembled = true;
        
        // init main screen and menus
        this.InitializeMainScreen();
        
        // change main screen title
        this.SetMainScreenTitle("Flash Card Viewer");
        
        // push screen
        this.PushMainScreen();
        
        
    }
    
    
    
    private VerticalFieldManager createViewerGui(boolean forAdmin)
    {
        if(forAdmin)
        {
            this.daWiz = new CardAdminWizard();
        }
        
        if(this.appThread.MainScreenInitialized)
        {
            // must be on card display
            addPreviousAndNextButtons();
        }
        else
        {
            
        }
        
        return new VerticalFieldManager();
    }
    
    private void addPreviousAndNextButtons()
    {
        
    }
    
    
    
    public class CardViewerApplicationThread extends IslandApplicationThread
    {
        // public variables
        public boolean MainScreenInitialized;
        public boolean WeAreRunning;
        public Stack viewerComponentStack;
        public VerticalFieldManager mainScreen; 
        
        // text fields
        public RichTextField rtf;
        
        // main screen buttons
        public ButtonField AdminViewerButton; 
        public ButtonField UserViewerButton;
        
        // private variables
        private boolean debug;
        
        public CardViewerApplicationThread()
        {
            super();
            
            this.MainScreenInitialized = false;
            this.WeAreRunning = false;
        
            this.mainScreen= new VerticalFieldManager(Manager.USE_ALL_WIDTH | Manager.USE_ALL_HEIGHT)
            {
                public void paint(Graphics graphics)
                {
                    try
                    {
                        // we need a rich text field for the header
                        if(rtf == null)
                        {
                            rtf = new RichTextField("Welcome to the Card Viewer");
                        }
                        
                        if(AdminViewerButton == null)
                        {
                            AdminViewerButton = new ButtonField("Create Flash Cards");
                            UserViewerButton = new ButtonField("View Flash Cards"); 
                        }
                        
                        CreateMainScreen();
                    }
                    catch(Exception ex)
                    {
                        if(debug)
                        {
                            System.out.println("[ERROR] problems painting the main screen:"+ex.toString());
                        }
                    }
                    
                    super.paint(graphics);
                }                            
            };
            
            debug = false;
            
            super.GuiAssembled = true;
        }
        
        
        public void CreateMainScreen()
        {
            try
            {
                if(viewerComponentStack == null)
                {
                    viewerComponentStack = new Stack();
                }   
                
                if(viewerComponentStack.isEmpty())
                {
                    // Add header text
                    viewerComponentStack.addElement(this.rtf);
                    
                    // we are just going to assume there is just 1 screen if empty
                    viewerComponentStack.addElement(this.AdminViewerButton);
                    viewerComponentStack.addElement(this.UserViewerButton);
                }
                
                // get all the stuff ot of he stack and add it it to the mainscreen
                for(int a = 0; a<viewerComponentStack.size(); a++)
                {
                    mainScreen.add((Field)viewerComponentStack.elementAt(a));
                }
                
            }
            catch(Exception ex)
            {
                if(debug)
                {
                    System.out.println("[ERROR] problems painting using the viewer components manager:"+ex.toString());
                    ex.printStackTrace();
                }
            }
        }
        
        /**
         * Overriden function called by run method
         * @return <description>
         */
        private boolean startApplication()
        {
            
            if(!this.MainScreenInitialized)
            {
                // add what's in the stack to our component mnager or at least check
                this.MainScreenInitialized = true;
            }
            else
            {
                // figure out what screen we are supposed to be on and render it
            }
            
            return true; // for testing
        }
        
        private boolean loadAvailableCards()
        {
            return true;// for testing
        }
        
        /**
         * Overriden function called by run method
         * @return <description>
         */
        private boolean loadApplication()
        {
            if(this.loadAvailableCards())
            {
            
            }
            
            return true; //for testing
        }
        
        /**
         * Overriden function called by run method
         */
        private void showApplicationGui()
        {
            // unpack the stack and show what needs showing
            // get all the stuff ot of he stack and add it it to the mainscreen
            for(int a = 0; a<viewerComponentStack.size(); a++)
            {
                mainScreen.add((Field)viewerComponentStack.elementAt(a));
            }
            // always update the display
            super.appScreen.refreshScreen("Viewer");
        }
    }
} 
