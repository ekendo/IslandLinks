/*
 * Main.java
 *
 * Created on May 17, 2005, 9:48 AM
 */

package ScreenRecord;
import java.awt.BorderLayout;
import java.awt.AWTException;
import java.awt.Robot;
import java.awt.Rectangle;
import java.awt.Toolkit;
import java.awt.image.BufferedImage;
import java.io.*;
import javax.imageio.ImageIO;
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
 *
 * @author Earl Kenyatta
 */
public class ScreenRecord 
{
    
    /** Creates a new instance of Main */
    public ScreenRecord() 
    {
    }
    
    /**
     * @param args the command line arguments
     */
    public static void main(String[] args) throws AWTException, IOException 
    {
     // Variable declaration.   
     JFrame frame = null;
     BufferedImage screencapture = null;
     File file = null;
     TransparentComponent bg = null;
     int x=0;
     int i = 0;
     int width = -1, height = -1, frameRate = 1;
     //Vector inputFiles = new Vector();
     MediaLocator oml;
     
     // Variable Assignments
     String outputURL = "file:///C:/ScreenRecord/demoScreenRecord.mov";
     //Rectangle screenSize = new Rectangle(Toolkit.getDefaultToolkit().getScreenSize());
     //width = screenSize.width;
     //height = screenSize.height;
     
     frame = new JFrame("RVM");
     bg = new TransparentComponent(frame, outputURL);
     bg.setLayout(new BorderLayout());
     frame.getContentPane().add("Center", bg);
     frame.pack();
     frame.setSize(Toolkit.getDefaultToolkit().getScreenSize());
     frame.show();
     
     /*
     // Main loop.
     while(x<15)
     {
         // capture the whole screen
         screencapture = new Robot().createScreenCapture(screenSize);

         // Save as JPEG
         String imageFileName = "screencapture" + x + ".jpg";
         file = new File(imageFileName);
         ImageIO.write(screencapture, "jpg", file);
         
         // Add to movie list.
         inputFiles.addElement("screencapture" + x + ".jpg");
         x++;
     }
     
      // Generate the output media locators.
	if ((oml = JpegImagesToMovie.createMediaLocator(outputURL)) == null) {
	    System.err.println("Cannot build media locator from: " + outputURL);
	    System.exit(0);
	}
     
     JpegImagesToMovie imageToMovie = new JpegImagesToMovie();
	imageToMovie.doIt(width, height, frameRate, inputFiles, oml);
       */
     
	//System.exit(0);
	
    }
    
}
