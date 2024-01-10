/*
 * JPegImagesToMovie.java
 *
 * Created on May 8, 2006, 10:50 AM
 */

package ScreenRecord;
import java.io.*;
import java.util.*;
import java.awt.Dimension;

import javax.media.*;
import javax.media.control.*;
import javax.media.protocol.*;
import javax.media.protocol.DataSource;
import javax.media.datasink.*;
import javax.media.format.VideoFormat;
/**
 * This program takes a list of JPEG image files and convert them into
 * a QuickTime movie.
 */
public class JPegImagesToMovie implements ControllerListener, DataSinkListener {

    public boolean doIt(int width, int height, int frameRate, Vector inFiles, MediaLocator outML) {
	ImageDataSource ids = new ImageDataSource(width, height, frameRate, inFiles);

	Processor p;

	try {
	    System.err.println("- create processor for the image datasource ...");
	    p = Manager.createProcessor(ids);
	} catch (Exception e) {
	    System.err.println("Yikes!  Cannot create a processor from the data source.");
	    return false;
	}

	p.addControllerListener(this);

	// Put the Processor into configured state so we can set
	// some processing options on the processor.
	p.configure();
	if (!waitForState(p, p.Configured)) {
	    System.err.println("Failed to configure the processor.");
	    return false;
	}

	// Set the output content descriptor to QuickTime. 
	p.setContentDescriptor(new ContentDescriptor(FileTypeDescriptor.QUICKTIME));

	// Query for the processor for supported formats.
	// Then set it on the processor.
	TrackControl tcs[] = p.getTrackControls();
	Format f[] = tcs[0].getSupportedFormats();
	if (f == null || f.length <= 0) {
	    System.err.println("The mux does not support the input format: " + tcs[0].getFormat());
	    return false;
	}

	tcs[0].setFormat(f[0]);

	System.err.println("Setting the track format to: " + f[0]);

	// We are done with programming the processor.  Let's just
	// realize it.
	p.realize();
	if (!waitForState(p, p.Realized)) {
	    System.err.println("Failed to realize the processor.");
	    return false;
	}

	// Now, we'll need to create a DataSink.
	DataSink dsink;
	if ((dsink = createDataSink(p, outML)) == null) {
	    System.err.println("Failed to create a DataSink for the given output MediaLocator: " + outML);
	    return false;
	}

	dsink.addDataSinkListener(this);
	fileDone = false;

	System.err.println("start processing...");

	// OK, we can now start the actual transcoding.
	try {
	    p.start();
	    dsink.start();
	} catch (IOException e) {
	    System.err.println("IO error during processing");
	    return false;
	}

	// Wait for EndOfStream event.
	waitForFileDone();

	// Cleanup.
	try {
	    dsink.close();
	} catch (Exception e) {}
	p.removeControllerListener(this);

	System.err.println("...done processing.");

	return true;
    }


    /**
     * Create the DataSink.
     */
    DataSink createDataSink(Processor p, MediaLocator outML) {

	DataSource ds;

	if ((ds = p.getDataOutput()) == null) {
	    System.err.println("Something is really wrong: the processor does not have an output DataSource");
	    return null;
	}

	DataSink dsink;

	try {
	    System.err.println("- create DataSink for: " + outML);
	    dsink = Manager.createDataSink(ds, outML);
	    dsink.open();
	} catch (Exception e) {
	    System.err.println("Cannot create the DataSink: " + e);
	    return null;
	}

	return dsink;
    }


    Object waitSync = new Object();
    boolean stateTransitionOK = true;

    /**
     * Block until the processor has transitioned to the given state.
     * Return false if the transition failed.
     */
    boolean waitForState(Processor p, int state) {
	synchronized (waitSync) {
	    try {
		while (p.getState() < state && stateTransitionOK)
		    waitSync.wait();
	    } catch (Exception e) {}
	}
	return stateTransitionOK;
    }


    /**
     * Controller Listener.
     */
    public void controllerUpdate(ControllerEvent evt) {

	if (evt instanceof ConfigureCompleteEvent ||
	    evt instanceof RealizeCompleteEvent ||
	    evt instanceof PrefetchCompleteEvent) {
	    synchronized (waitSync) {
		stateTransitionOK = true;
		waitSync.notifyAll();
	    }
	} else if (evt instanceof ResourceUnavailableEvent) {
	    synchronized (waitSync) {
		stateTransitionOK = false;
		waitSync.notifyAll();
	    }
	} else if (evt instanceof EndOfMediaEvent) {
	    evt.getSourceController().stop();
	    evt.getSourceController().close();
	}
    }


    Object waitFileSync = new Object();
    boolean fileDone = false;
    boolean fileSuccess = true;

    /**
     * Block until file writing is done. 
     */
    boolean waitForFileDone() {
	synchronized (waitFileSync) {
	    try {
		while (!fileDone)
		    waitFileSync.wait();
	    } catch (Exception e) {}
	}
	return fileSuccess;
    }


    /**
     * Event handler for the file writer.
     */
    public void dataSinkUpdate(DataSinkEvent evt) {

	if (evt instanceof EndOfStreamEvent) {
	    synchronized (waitFileSync) {
		fileDone = true;
		waitFileSync.notifyAll();
	    }
	} else if (evt instanceof DataSinkErrorEvent) {
	    synchronized (waitFileSync) {
		fileDone = true;
		fileSuccess = false;
		waitFileSync.notifyAll();
	    }
	}
    }

    /*
    public static void main(String args[]) {

	if (args.length == 0)
	    prUsage();

	// Parse the arguments.
	int i = 0;
	int width = -1, height = -1, frameRate = 1;
	Vector inputFiles = new Vector();
	String outputURL = null;

	while (i < args.length) {

	    if (args[i].equals("-w")) {
		i++;
		if (i >= args.length)
		    prUsage();
		width = new Integer(args[i]).intValue();
	    } else if (args[i].equals("-h")) {
		i++;
		if (i >= args.length)
		    prUsage();
		height = new Integer(args[i]).intValue();
	    } else if (args[i].equals("-f")) {
		i++;
		if (i >= args.length)
		    prUsage();
		frameRate = new Integer(args[i]).intValue();
	    } else if (args[i].equals("-o")) {
		i++;
		if (i >= args.length)
		    prUsage();
		outputURL = args[i];
	    } else {
		inputFiles.addElement(args[i]);
	    }
	    i++;
	}

	if (outputURL == null || inputFiles.size() == 0)
	    prUsage();

	// Check for output file extension.
	if (!outputURL.endsWith(".mov") && !outputURL.endsWith(".MOV")) {
	    System.err.println("The output file extension should end with a .mov extension");
	    prUsage();
	}

	if (width < 0 || height < 0) {
	    System.err.println("Please specify the correct image size.");
	    prUsage();
	}

	// Check the frame rate.
	if (frameRate < 1)
	    frameRate = 1;

	// Generate the output media locators.
	MediaLocator oml;

	if ((oml = createMediaLocator(outputURL)) == null) {
	    System.err.println("Cannot build media locator from: " + outputURL);
	    System.exit(0);
	}

	JpegImagesToMovie imageToMovie = new JpegImagesToMovie();
	imageToMovie.doIt(width, height, frameRate, inputFiles, oml);

	System.exit(0);
    }
    */
    
    static void prUsage() {
	System.err.println("Usage: java JpegImagesToMovie -w <width> -h <height> -f <frame rate> -o <output URL> <input JPEG file 1> <input JPEG file 2> ...");
	System.exit(-1);
    }

    /**
     * Create a media locator from the given string.
     */
    static MediaLocator createMediaLocator(String url) {

	MediaLocator ml;

	if (url.indexOf(":") > 0 && (ml = new MediaLocator(url)) != null)
	    return ml;

	if (url.startsWith(File.separator)) {
	    if ((ml = new MediaLocator("file:" + url)) != null)
		return ml;
	} else {
	    String file = "file:" + System.getProperty("user.dir") + File.separator + url;
	    if ((ml = new MediaLocator(file)) != null)
		return ml;
	}

	return null;
    }
}