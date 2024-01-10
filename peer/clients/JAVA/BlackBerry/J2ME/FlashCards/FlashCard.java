/*
 * FlashCard.java
 *
 * © <your company here>, 2003-2005
 * Confidential and proprietary.
 */

package com.CAF.card;


import java.lang.*;


/**
 * 
 */
public class FlashCard 
{
    private String audioFileLocation = "";
    private String imageFileLocation = "";
    private String cardText = "";
    
    public FlashCard() 
    {    
    
    }
    
    public void SetCardText(String t)
    {
        this.cardText = t;
    }
    
    public void SetCardAudioFile(String aF)
    {
        this.audioFileLocation = aF;
    } 
    
    public void SetCardImageFileLocation(String imgF)
    {
        this.imageFileLocation = imgF;
    }
    
    public String GetCardAudioFile()
    {
        return this.audioFileLocation;
    }
    
    public boolean playCardAudio()
    {
        return false;
    }
    
    public boolean showCardImage()
    {
        return false;
    }
    
    public boolean showCardText()
    {
        return false;
    }
    
} 
