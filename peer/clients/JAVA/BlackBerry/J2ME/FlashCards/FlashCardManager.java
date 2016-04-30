/*
 * FlashCardManager.java
 *
 * © EKenDo, LLC , 2003-2005
 * Confidential and proprietary.
 */

package com.CAF.card;

import java.lang.*;
import java.util.*;
import com.CAF.card.FlashCard;
/**
 * 
 */
public class FlashCardManager 
{
    private FlashCard card;
    
    public FlashCardManager() 
    {    
        card = new FlashCard();
    }
    
    public String GetAudioFile()
    {
        return card.GetCardAudioFile();
    }
    
    public void SetAudioFile()
    {
        
    }
    
    
} 
