package com.airliquide.alps.client.manager 
{
	// AS3 SDK
	import flash.events.Event;
	import flash.xml.*;
	
	// flex SDK
	import mx.core.Application;
	import mx.managers.*;
	import mx.collections.*;
	import mx.rpc.http.*;
	import mx.rpc.events.*;
	import mx.controls.Alert;
	import mx.controls.Label;
	
	// airliquide SDK
	import com.airliquide.alps.client.*;
	
	/**
	 * ...
	 * @author earl.lawrence
	 */
	public class ViewerUI extends Application
	{
		
		public var lblVersion:Label;
		public var appLoader:ChartAppLoader;
		
		
		public function ViewerUI() 
		{
			super();
			
			this.lblVersion = new Label();
			this.lblVersion.height = 1000;
			this.lblVersion.width = 2000;
			
			this.appLoader = new ChartAppLoader();
		}
		
		public function initVars():void
		{
			this.lblVersion.text = "we just called the init vars location";
			
			try
			{
				this.appLoader.GetVariables(this);
			
				//this.lblVersion.text = this.appLoader.GetDebugTxt().text;
				this.lblVersion.text = this.appLoader.GetDebugTxt();// + "*";
				
			}
			catch (err:Error)
			{
				this.lblVersion.text = "[ERROR] problems loading variables:"+err.message;
			}
		}
		
		public function appComplete(e:Event):void
		{
			
		}
	}

}