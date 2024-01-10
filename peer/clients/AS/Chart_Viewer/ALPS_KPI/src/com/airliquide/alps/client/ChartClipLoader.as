package com.airliquide.alps.client
{
	// AS3 SDK
	import flash.display.DisplayObject;
	import flash.display.MovieClip;
	import flash.events.Event;
	import flash.events.ProgressEvent;
	import flash.text.TextField;
	import flash.utils.getDefinitionByName;
	
	// Flex SDK
	import mx.controls.*;
	
	// Airliquide SDK
	import com.airliquide.alps.lang.*;
	import com.airliquide.alps.chart.*;
	import com.airliquide.alps.client.*;
	import com.airliquide.alps.client.manager.*;
	
	/**
	 * @author earl.lawrence
	 * 
	 * Class responsible for getting the values from 
	 * the data to the Viewer which will create
	 * and instantiate the actual chart.
	 */
	public class ChartClipLoader extends MovieClip 
	{
		private var viewer:ChartViewer;
		private var viewerClass:Class;
		private var chartValues:Map;
		private var chartData:Map;
		private var categoryValues:Array;
		private var fieldValues:Array;
		private var dataKeys:Array;
		private var dataKeyValues:Array;
		
		// debug
		private var txtTest:TextField = new TextField();
		
		public function ChartClipLoader() 
		{
			addEventListener(Event.ENTER_FRAME, checkFrame);
			loaderInfo.addEventListener(Event.COMPLETE, getVariables);
			loaderInfo.addEventListener(ProgressEvent.PROGRESS,progress);
			
			// load up the chart Vals
			this.chartValues = new Map();
			this.chartData = new Map();
			
			txtTest.text = "YoYo";
			txtTest.x = 0;
			txtTest.y = 100;
			txtTest.width = 1000;
			txtTest.height = 100;
			
			this.addChild(txtTest);
		}
		
		private function progress(e:ProgressEvent):void 
		{
			// update loader
		}
		
		private function getVariables(e:Event):void 
		{
			// get chart stuffs
			if (loaderInfo.parameters.chartRenderrer != null)
			{
				this.chartValues.setValue("Renderrer",loaderInfo.parameters.chartRenderrer);
				txtTest.text = "chartRenderer ain null";
			}
			
			if (loaderInfo.parameters.chartDisplayFormat != null)
			{
				this.chartValues.setValue("DisplayFormat",loaderInfo.parameters.chartDisplayFormat);
				txtTest.text = "chartDisplayFormat ain null";
			}
			
			if (loaderInfo.parameters.chartCategories != null)
			{
				this.chartValues.setValue("Categories", loaderInfo.parameters.chartCategories);
				this.categoryValues = chartValues.getValue("Categories").split(",");
				txtTest.text = "chartCategories ain null";
			}
			
			if (loaderInfo.parameters.chartFields != null)
			{
				this.chartValues.setValue("Fields",loaderInfo.parameters.chartFields);
				txtTest.text = "chartFields ain null";
			}
			
			if (loaderInfo.parameters!=null)
			{
				this.chartValues.setValue("DataType","HTML");
			}
			
			// get field stuffs
			if (this.chartValues.length() > 0)
			{
				// get Field values
				this.getFieldValues(this.chartValues)
			
				//txtTest.text = "called getFieldValues";
				
				this.getChartDataByCategory(this.fieldValues)
				
				//txtTest.text = "called getData By Category";
				
			}
			
		}
		
		/**
		 * for each category if the category is Chart_By or Filter_By
		 * get the field key values split them into an array and iterate over
		 * them nesting by value.
		 * @param	f
		 */
		private function getChartDataByCategory(f:Array):void
		{
			var i:Number = 0;
			var j:Number = 0;
			var k:Number = 0;
			var dataKeyValueElements:Array;
			var chartDataKey:String;
			var chartDataValues:String;
			var chartDataValueKeyName:String = "";
			
			// generate all the keys first
			for (i = 0; i < this.categoryValues.length;i++ )
			{
				if (this.categoryValues[i] != null)
				{
					if((this.categoryValues[i].indexOf("Chart_By")>=0)||(this.categoryValues[i].indexOf("Filter_By")>=0))
					{
						// loop through the chart fields => Operator
						//for (j = 0; j < this.dataKeys.length; j++ )
						{
							if (i == 0)
							{
								// 1st elements
								dataKeyValueElements = this.dataKeyValues[i].split(",");
								//this.txtTest.appendText("*:"+dataKeyValueElements[0]+"*");
							}
							else
							{
								var tmp:Array = dataKeyValues[i].split(",");
								var tmp2:Array = new Array();
								
								//this.txtTest.appendText(">:"+dataKeyValues[j]+"<");
								
								for (k = 0; k < dataKeyValueElements.length;k++ )
								{
									var s:String = dataKeyValueElements[k];
									//this.txtTest.appendText("dkve:"+dataKeyValueElements[k]);
									
									for (var m:Number = 0; m < tmp.length;m++ )
									{
										s = dataKeyValueElements[k] + "_" + tmp[m];
										//break;
										//this.txtTest.appendText("{" + s + "}("+i+")");
										
										tmp2.push(s);
									}
									
									//tmp2.push(s);
									//this.txtTest.appendText("{"+s+"}");
									//break;
								}
								
								dataKeyValueElements = tmp2;
							}
						
							//this.txtTest.appendText("*:"+dataKeys[j]);
							//break;
						}	
							
					}
				}
				
				//this.txtTest.text = dataKeyValueElements[0];
				//break;
			}
			
			this.txtTest.text = "";
			
			// now get the chart data by key
			for (j = 0; j < dataKeyValueElements.length; j++ )
			{
				//this.txtTest.appendText("{" + dataKeyValueElements[j] + "}");
				
				var dataKey:String = dataKeyValueElements[j] + "_Values";
				
				if (this.loaderInfo.parameters[dataKey] != null)
				{
					this.chartData.setValue(dataKey, this.loaderInfo.parameters[dataKey]);
					this.txtTest.appendText("{" + dataKey + "-loaded}");
				}
			}
		}
		
		private function getFieldValues(c:Map):void
		{
			var i:Number = 0;
			var key:String;
			var value:String;
			this.dataKeys = new Array();
			this.dataKeyValues = new Array();
			this.txtTest.text = "here at getFildVals";
			
			if (this.fieldValues == null)
			{
				for (i = 0; i < c.length();i++ )
				// loop through chart values looking for 
				{
					var me:MapEntry = c.getEntryAt(i);
					
					if (me != null)
					{
						if (me.key.indexOf("Fields") >= 0)
						{
							this.fieldValues = me.value.split(",");
						}
					}
				}
			}
		
			this.txtTest.text = "";
			
			// loop through categories and create label keys & values
			for (i = 0; i < this.fieldValues.length; i++ )
			{
				if (this.fieldValues[i] != null)
				{
					if (this.fieldValues[i].indexOf(":") < 0)
					{
						key = this.fieldValues[i] + "_Values";
						value = loaderInfo.parameters[key];
						
						this.dataKeys.push(key);
						this.dataKeyValues.push(value);
					
						this.txtTest.text = key +":" + value;
					}
				}
			}
		}
		
		private function checkFrame(e:Event):void 
		{
			if (currentFrame == totalFrames) 
			{
				removeEventListener(Event.ENTER_FRAME, checkFrame);
				
				startup();
			}
		}
		
		private function startup():void 
		{
			// hide loader
			stop();
			
			loaderInfo.removeEventListener(ProgressEvent.PROGRESS, progress);
			
			viewerClass = getDefinitionByName("com.airliquide.alps.client.ChartViewer") as Class;
			
			// set the variables we loaded
			viewer = new viewerClass() as ChartViewer; 
			
			
			//addChild(new viewerClass() as DisplayObject);
			addChild(viewer);
			
			// set the maps
			viewer.SetChartInfo(this.chartValues);
			viewer.SetChartData(this.chartData);
				
			// set the arrays
			viewer.SetChartCategories(this.categoryValues);
			viewer.SetChartFields(this.fieldValues);
			
			viewer.CreateChart();
			
			viewer.DrawChart();
			
			this.txtTest.appendText("started up");
		}
		
	}
	
}