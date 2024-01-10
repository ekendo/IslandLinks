/*
 * Main.java
 *
 * Created on August 22, 2006, 8:38 PM
 */

package com.ekendotech.homeisland;

import javax.swing.*;
/**
 *
 * @author EKenDo
 *
 *  Class used as the main entry point into island links.
 *  This will manage the windows necessary to start and manage
 *  the framework functionality.
 */
public class IslandLinksMain {

    /**
     * Create the GUI and show it.  For thread safety,
     * this method should be invoked from the
     * event-dispatching thread.
     */
    private static void createAndShowGUI() {
        //Make sure we have nice window decorations.
        JFrame.setDefaultLookAndFeelDecorated(true);

        //Create and set up the window.
        JFrame frame = new JFrame("Island");
        frame.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);

        //Add the ubiquitous "Hello World" label.
        JLabel label = new JLabel("Home Island Portal");
        frame.getContentPane().add(label);

        //Display the window.
        frame.pack();
        frame.setVisible(true);
    }

    /** Creates a new instance of Main */
    public IslandLinksMain()
    {
    }

    /**
     * @param args the command line arguments
     */
    public static void main(String[] args)
    {
        //Schedule a job for the event-dispatching thread:
        //creating and showing this application's GUI.
        javax.swing.SwingUtilities.invokeLater(
            new Runnable()
                {
                    public void run()
                    {
                        createAndShowGUI();
                    }
                }
        );
    }
}
