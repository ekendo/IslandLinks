/*
 * TransparentComponent.java
 * Created on May 7, 2006
 */
package ScreenRecord;

import java.awt.*;
import java.awt.BorderLayout;
import java.awt.Dimension;
import java.awt.Graphics;
import java.awt.Image;
import java.awt.Point;
import java.awt.Rectangle;
import java.awt.Robot;
import java.awt.Toolkit;
import java.awt.event.ComponentEvent;
import java.awt.event.ComponentListener;
import java.awt.event.WindowEvent;
import java.awt.event.WindowFocusListener;
import java.awt.event.WindowListener;
import java.util.Date;
import java.awt.image.BufferedImage;
import java.io.*;
import javax.imageio.ImageIO;
import javax.imageio.stream.*;
import java.util.*;

import javax.media.*;
import javax.media.control.*;
import javax.media.protocol.*;
import javax.media.protocol.DataSource;
import javax.media.datasink.*;
import javax.media.format.VideoFormat;

import javax.swing.JButton;
import javax.swing.JComponent;
import javax.swing.JFrame;
import javax.swing.JLabel;

/**
 * TransparentComponent gives the appearance of a transparent swing window 
 * by periodically taking a screen capture and displaying the contents with in 
 * resizeable "transparent" window.
 * 
 * @author O'Neil Palmer
 */
public class TransparentComponent  extends JComponent implements ComponentListener,
                                                                WindowFocusListener, 
                                                                WindowListener,
                                                                Runnable{
	/** frame */
	private JFrame frame;
	/** Background image used for screen capture. */
	private Image background;
	/** Record the last time the screen component was updated. */
	private long lastUpdated = 0;
	/** Set to true when the component should be requested. */
	private boolean refreshRequested = true;
        private Dimension dim;
        private Graphics offGraphics = null;
        private int vectorIndex = 0;
        private int height, width;
        
        public Vector inputFiles = new Vector();
        public MediaLocator oml;
	/**
	 * Constructor.  Creates a new TransparentComponent with the default initial size.
	 */
	public TransparentComponent(JFrame frame, String movieLocation) {
		super();
		this.frame = frame;
		frame.setDefaultCloseOperation(JFrame.HIDE_ON_CLOSE);
		updateBackground(0);
		frame.addComponentListener(this);
                frame.addWindowListener(this);
		frame.addWindowFocusListener(this);
                frame.setState(frame.ICONIFIED);
                // Generate the output media locators.
                if ((oml = JPegImagesToMovie.createMediaLocator(movieLocation)) == null) 
                {
                    System.err.println("Cannot build media locator from: " + movieLocation);
                    System.exit(0);
                }
		new Thread(this).start();
	}
	/**
	 * Captures the contents of the screen and updates the background image.
	 * 
	 */
	private void updateBackground(int mode) {
		try {
			Robot rbt = new Robot();
			//Toolkit tk = frame.getToolkit().getDefaultToolkit();
			Toolkit tk = Toolkit.getDefaultToolkit();
                        dim = tk.getScreenSize();
                        width = frame.getWidth()-10;
                        height  = frame.getHeight()-35;
                        if((frame.getX()+5)>=0&&(frame.getY()+30)>=0)
                        {
                            
                            
                            
                            //background = rbt.createScreenCapture(new Rectangle((frame.getX()),(frame.getY()),(frame.getWidth()),(frame.getHeight())));
                            BufferedImage screencapture = rbt.createScreenCapture(new Rectangle((frame.getX()+5),(frame.getY()+30),(width),(height)));
                            //background = rbt.createScreenCapture(frame.getBounds());
                            //repaint();
                            if(mode==1)
                            {
                                Point location = frame.getLocation();
                                frame.setLocation(-frame.getWidth(), -frame.getHeight());
                                background = rbt.createScreenCapture(new Rectangle(dim));
                                frame.setLocation(location);
                            }
                            
                             // Save as JPEG
                            //String imageFileName = "screencapture.jpg";
                            //File file = new File(imageFileName);
                            ByteArrayOutputStream baos = new ByteArrayOutputStream();
                            //ImageOutputStream ios = ImageIO.createImageOutputStream(screencapture);
                            //ImageOutputStreamImpl ios = new ImageOutputStreamImpl();
                            ImageIO.write(screencapture,"JPEG",baos);
                            inputFiles.add(vectorIndex, baos);
                            vectorIndex++;
                        }
                } catch (Exception e) {
			System.err.println(e.toString());
			e.printStackTrace();
		}
		
	}
	
        public void paint(Graphics g) {
            update(g);
        }
        /**
         * Paint a frame of animation.
         */
        public void paintFrame(Graphics g) {
            g.drawImage(background, 0, 0, null);
        }
        
        /**
         * Update a frame of animation.
         */
        public void update(Graphics g) {
           
            Point pos = this.getLocationOnScreen();
            Point offset = new Point(-pos.x, -pos.y);
            
            // Paint the image onto the screen
            g.drawImage(background, offset.x, offset.y, null);
        }
        
	/*
	 * @see java.awt.event.ComponentListener#componentHidden(java.awt.event.ComponentEvent)
	 */
	public void componentHidden(ComponentEvent e) {		
	}
	/* 
	 * @see java.awt.event.ComponentListener#componentMoved(java.awt.event.ComponentEvent)
	 */
	public void componentMoved(ComponentEvent e) {
            repaint();
            
	}
	/*
	 * @see java.awt.event.ComponentListener#componentResized(java.awt.event.ComponentEvent)
	 */
	public void componentResized(ComponentEvent e) {
            this.inputFiles.clear();
            this.vectorIndex = 0;
            repaint();
		
	}
	/*
	 * @see java.awt.event.ComponentListener#componentShown(java.awt.event.ComponentEvent)
	 */
	public void componentShown(ComponentEvent e) {
		repaint();
		
	}
	/* (non-Javadoc)
	 * @see java.awt.event.WindowFocusListener#windowGainedFocus(java.awt.event.WindowEvent)
	 */
	public void windowGainedFocus(WindowEvent e) {
		//updateBackground(1);
                repaint();
                
	}
	/* (non-Javadoc)
	 * @see java.awt.event.WindowFocusListener#windowLostFocus(java.awt.event.WindowEvent)
	 */
	public void windowLostFocus(WindowEvent e) {
                frame.setState(frame.ICONIFIED);
		refresh();
	}
	
        public void windowClosed(WindowEvent event) 
        {
             
        }
        
        public void windowClosing(WindowEvent e) {
           JPegImagesToMovie imageToMovie = new JPegImagesToMovie();
           imageToMovie.doIt(width, height, 1, inputFiles, oml);
           System.exit(0);
        }

         public void windowOpened(WindowEvent e) {
        //displayMessage("WindowListener method called: windowActivated.");
        }
         
        public void windowActivated(WindowEvent e) {
        //displayMessage("WindowListener method called: windowActivated.");
        }

        public void windowDeactivated(WindowEvent e) {
        //displayMessage("WindowListener method called: windowDeactivated.");
        }
        
        public void windowIconified(WindowEvent e) {
        //displayMessage("WindowListener method called: windowIconified.");
        }

        public void windowDeiconified(WindowEvent e) {
        //displayMessage("WindowListener method called: windowDeiconified.");
        }
	/**
	 * Refresh the component.
	 */
	private void refresh() {
		//if(frame.isVisible()){
			repaint();
			refreshRequested = true;
			lastUpdated = new Date().getTime();
		//}		
	}
	
	/* 
	 * Refreshes the screen periodically.
	 * 
	 * @see java.lang.Runnable#run()
	 */
	public void run() {
		try {
			while (true) 
                        {
                              if(frame.isVisible())
                              {
                                  Thread.sleep(500);
                                  long now = System.currentTimeMillis();
                                  if(refreshRequested &&(now - lastUpdated) > 1000) 
                                  {
                                    updateBackground(1);
                                    refresh();
                                  }
                              }
                              else
                              {
                                updateBackground(0);
                                refresh();
                              }
			}
			
		} catch (Exception e) {
			System.err.println(e.toString());
			e.printStackTrace();
		}
	
	}
}
