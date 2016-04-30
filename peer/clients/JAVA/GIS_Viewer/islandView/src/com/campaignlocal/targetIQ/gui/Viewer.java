/*
 * Viewer.java
 *
 * Created on June 16, 2007, 12:37 AM
 *
 * To change this template, choose Tools | Template Manager
 * and open the template in the editor.
 */

package com.campaignlocal.targetIQ.gui;


import java.awt.BorderLayout;
import java.awt.Color;
import java.awt.Container;
import java.awt.Dimension;
import java.awt.Panel;
import java.awt.ScrollPane;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.awt.RenderingHints;
import java.awt.image.BufferedImage;
import java.awt.Graphics2D;

import javax.swing.Action;
import javax.swing.Box;
import javax.swing.BoxLayout;
import javax.swing.ImageIcon;
import javax.swing.JButton;
import javax.swing.JFrame;
import javax.swing.JLabel;
import javax.swing.JOptionPane;
import javax.swing.JPanel;
import javax.swing.JTextArea;
import javax.swing.JToolBar;
import javax.swing.WindowConstants;

import org.geotools.gui.swing.JMapPane;
import org.geotools.gui.swing.PanAction;
import org.geotools.gui.swing.ResetAction;
import org.geotools.gui.swing.SelectAction;
import org.geotools.gui.swing.ZoomInAction;
import org.geotools.gui.swing.ZoomOutAction;
import org.geotools.map.DefaultMapContext;
import org.geotools.map.DefaultMapLayer;
import org.geotools.map.MapContext;
import org.geotools.referencing.CRS;
import org.geotools.referencing.crs.DefaultGeographicCRS;
import org.geotools.renderer.GTRenderer;
import org.geotools.renderer.lite.StreamingRenderer;

import org.opengis.referencing.FactoryException;
import org.opengis.referencing.crs.CoordinateReferenceSystem;

import com.campaignlocal.targetIQ.main.ViewerManager;

/**
 *
 * @author Administrator
 */
public class Viewer {
    
    /* A link to the ViewerManager instance, to pass control back to the 'button*'
     * methods in that instance. */
    final ViewerManager viewerBase;
    
    /* GUI frame, pane and extras */
    //final MainViewerFrame frame;
    public final JFrame frame;
    JPanel visPanel;
    ScrollPane infoSP;
    JToolBar jtb;
    JLabel text;
    JButton quitButton;
      
    /* Display elements */
    public JMapPane jmp;
    public MapContext context;
    public GTRenderer renderer;
    
    com.vividsolutions.jts.geom.Envelope worldbounds;
    
    /** Creates a new instance of ViewerGUI */
    public Viewer(ViewerManager vb) 
    {
        this.viewerBase = vb; 
        int BUTTON_WIDTH = 100;
        
        //frame = new MainViewerFrame();
        frame=new JFrame("TargetIQ Demo");
        frame.setDefaultCloseOperation(WindowConstants.EXIT_ON_CLOSE);
        frame.setBounds(20,20,900,500);
        
        Container contentPane = frame.getContentPane();
        BoxLayout layout = new BoxLayout(contentPane, BoxLayout.X_AXIS);
        contentPane.setLayout(layout);
        
        JPanel functionalityPanel = new JPanel();
        functionalityPanel.setVisible(true);
        
        visPanel = new JPanel();
        visPanel.setVisible(true);
        
        contentPane.add(Box.createRigidArea(new Dimension(10, 0)));
        contentPane.add(functionalityPanel);
        contentPane.add(Box.createRigidArea(new Dimension(10, 0)));
        contentPane.add(visPanel);
        contentPane.add(Box.createRigidArea(new Dimension(10, 0)));
        
        /* The action button Panel */
        JPanel actionButtonPanel = new JPanel();
        actionButtonPanel.setVisible(true);
        
        BoxLayout aBPlayout = new BoxLayout(actionButtonPanel, BoxLayout.X_AXIS);
        actionButtonPanel.setLayout(aBPlayout);
         
        DemographicValueEditor demographicControlPanel = new DemographicValueEditor(this.viewerBase);
        FeatureManager activeMapPanel = new FeatureManager(this.viewerBase,demographicControlPanel);
        demographicControlPanel.InitDemographicInfo();
        
        /* The button Panel */
        BoxLayout buttonPanelBoxLayout = new BoxLayout(functionalityPanel,BoxLayout.Y_AXIS);
        functionalityPanel.setLayout(buttonPanelBoxLayout);
        functionalityPanel.add(Box.createRigidArea(new Dimension(0, 10)));
        functionalityPanel.add(activeMapPanel);
        functionalityPanel.add(Box.createRigidArea(new Dimension(0, 10)));
        functionalityPanel.add(demographicControlPanel);
        functionalityPanel.add(Box.createRigidArea(new Dimension(0, 20)));
        //functionalityPanel.add(Box.createVerticalGlue());
        
        JButton quitButton = new JButton("QUIT");
        JButton resetButton = new JButton("RESET");
        
        actionButtonPanel.add(resetButton);
        actionButtonPanel.add(Box.createRigidArea(new Dimension(10,0)));
        actionButtonPanel.add(quitButton);
        
        functionalityPanel.add(actionButtonPanel);
        functionalityPanel.add(Box.createRigidArea(new Dimension(0, 10)));
        
        quitButton.addActionListener(new ActionListener() {
            public void actionPerformed(ActionEvent e) {
                
                frame.dispose();
                
              }
          });
          
        // load Data for world Map
        viewerBase.loadSpatialData();
          
        System.out.println("before creating feature styles");
        
        // create country shapefile Styles
        this.createCountries();
        
        // create state shapefile Styles
        this.createStates();
        
        // create zipcode shapefile Styles
        this.createZipCodes();
        
        System.out.println("after creating feature styles");
        
          /* The info Text Area */
        infoSP = new ScrollPane();
        
        /* The visuals Panel */
        BoxLayout visPanelBoxLayout = new BoxLayout(visPanel,BoxLayout.Y_AXIS);
        visPanel.setLayout(visPanelBoxLayout);
        
        visPanel.add(Box.createRigidArea(new Dimension(0, 10)));
        //visPanel.add(infoSP);
        visPanel.add(Box.createRigidArea(new Dimension(0, 10)));
        
        contentPane.setVisible(true);
        contentPane.doLayout();
        
        frame.doLayout();
        //frame.setVisible(true);
        frame.repaint();
        
    }
    
    /*
     * Create a GUI map displayer.
     * 
     * This is all Swing stuff for the JMapPane.
     * 
     */
    public void initialize_JMapPane() throws Exception
    {
        //textArea.append("Start: Initialize the GUI.\n");

        Panel mapGUI = new Panel();
        mapGUI.setLayout(new BorderLayout());
        
        jmp = new JMapPane();
        jmp.setBackground(Color.white);
         
        /* Context */
        context = new DefaultMapContext(DefaultGeographicCRS.WGS84); 
        context.setAreaOfInterest(viewerBase.envlp_NoEdges2);
        
         /* Renderer */
        renderer = new StreamingRenderer();
        renderer.setContext(context);
        
        RenderingHints h = new RenderingHints(RenderingHints.KEY_INTERPOLATION,RenderingHints.VALUE_INTERPOLATION_NEAREST_NEIGHBOR);
        h.put(RenderingHints.KEY_RENDERING, RenderingHints.VALUE_RENDER_SPEED);
        h.put(RenderingHints.KEY_ANTIALIASING,RenderingHints.VALUE_ANTIALIAS_ON);
        h.put(RenderingHints.KEY_COLOR_RENDERING,RenderingHints.VALUE_COLOR_RENDER_SPEED);
        h.put(RenderingHints.KEY_DITHERING,RenderingHints.VALUE_DITHER_DISABLE);

        renderer.setJava2DHints(h);
         
         /* Add to JMapPane */
        jmp.setRenderer(renderer);
        jmp.setContext(context);
        
        /* The toolbar */
        jtb = new JToolBar();
        
        Action zoomIn = new ZoomInAction(jmp); 
        Action zoomOut = new ZoomOutAction(jmp);
        Action pan = new PanAction(jmp);
        Action select = new SelectAction(jmp);
        Action reset = new ResetAction(jmp);
        
        jtb.add(zoomIn);
        jtb.add(zoomOut);
        jtb.add(pan);
        jtb.addSeparator();
        jtb.add(reset);
        jtb.addSeparator();
        jtb.add(select);
         
        mapGUI.add(jtb,BorderLayout.NORTH);
        mapGUI.add(jmp);
        
        //infoSP.setSize(new Dimension(60,60));        
        BoxLayout visPanelBoxLayout = new BoxLayout(visPanel,BoxLayout.Y_AXIS);
        visPanel.setLayout(visPanelBoxLayout);
        
        //visPanel.add(Box.createRigidArea(new Dimension(0, 10)));
        //visPanel.add(infoSP);
        visPanel.add(Box.createRigidArea(new Dimension(0, 10)));
        visPanel.add(mapGUI);
        visPanel.add(Box.createRigidArea(new Dimension(0, 10)));
     
        frame.getContentPane().doLayout();
        //infoSP.setSize(new Dimension(3,3));
        //frame.getContentPane().doLayout();
        
        //this.viewerBase.DrawMap(ViewerManager.UrbanArea.HOUSTON);
        
        frame.setVisible(true);

        //textArea.append("  End: Initialized the GUI.\n");
        
    }
    
    /*
     *
     *
     */
    private void createCountries()
    {
         viewerBase.createFeatureStyles("shpstyl");
    }
    
    /*
     *
     */
    private void createStates()
    {
        viewerBase.createFeatureStyles("shpstyl2");
        
        viewerBase.createFeatureStyles("shpstyl3");
        
        viewerBase.createFeatureStyles("shpstyl4");
        
        viewerBase.createFeatureStyles("shpstyl5");
    }
    
    private void createZipCodes()
    {
        viewerBase.createFeatureStyles("txZipStyles");
        
        viewerBase.createFeatureStyles("ilZipStyles");
        
        viewerBase.createFeatureStyles("dcZipStyles");

         viewerBase.createFeatureStyles("caZipStyles");
    }
    
}
