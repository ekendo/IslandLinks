package com.airliquide.alps.client
{
	// AS3 SDK
	import adobe.utils.CustomActions;
	import flash.display.Sprite;
	import flash.events.*;
	import flash.text.TextField;
	import flash.utils.*;
	
	
	// FLEX SDK
	import mx.controls.*;
	import mx.containers.HBox;
	import mx.containers.VBox;
	import mx.events.*;
	
	// AirLiquide SDK
	import com.airliquide.alps.chart.*;
	import com.airliquide.alps.lang.*;
	import com.airliquide.alps.chart.type.HTMLChart;
	import com.airliquide.alps.client.manager.ViewerUI;
	import com.airliquide.alps.client.manager.MultiSelectDropDownCtl;
	
	/**
	 * ...
	 * @author earl.lawrence
	 */
	public class ChartViewer extends ViewerUI 
	{
		public static const millisecondsPerMinute:int = 1000 * 60;
		public static const millisecondsPerHour:int = 1000 * 60 * 60;
		public static const millisecondsPerDay:int = 1000 * 60 * 60 * 24;

		private var dbgText:Label;
		private var alChart:ALChart;
		
		// UI Components
		private var chartHeader:HBox;
		private var chartSelectors:HBox;
		private var chartSelector:VBox;
		private var chartFilter:MultiSelectDropDownCtl;
		private var chartStyles:ComboBox;
		private var chartDisplays:ComboBox;
		private var chartSubChart:LinkButton;
		private var chartByLabel:Label;
		private var chartByComboBox:MultiSelectDropDownCtl;
		private var chartMessages:TextArea;
		private var chartMessagesLabel:Label;
		private var customDateRangeLabel:Label;
		private var chartStartLabel:Label;
		private var chartEndLabel:Label;
		private var chartStartDate:LinkButton;
		private var chartEndDate:LinkButton;
		private var chartStartChooser:DateChooser;
		private var chartEndChooser:DateChooser;
		private var chartCustomSubChart:LinkButton;
		private var cbs:ComboBox;
		
		// containers
		private var chartFilters:Array;
		private var someChartFilters:Array;
		private var chartSubCharts:Array;
		private var someChartSubCharts:Array;
		private var chartByLabels:Array;
		private var chartByComboBoxes:Array;
		private var filterByComboBoxes:Array;
		private var chartMetricAliasKeys:Array;
		private var dateIndexes:Array = new Array();
		private var allPossibleDates:Array = new Array();
		private var allPossibleMetrics:Array = new Array();
		private var dateStartIndexes:Array = new Array();
		private var dateValueLengths:Array = new Array();
		private var monthMetrics:Array = new Array();
		private var validMetrics:Array = new Array();
		
		// Strings
		private var xLabel:String;
		private var yLabel:String;
		private var calculationLabel:String;
		private var rawDatesKey:String = "";
		private var rawDatesAliasKey:String = "";
		private var newKey:String;
		private var newAliasKey:String;
		private var newChartData:String = "";
		private var date:String = "";
			
		// Flags
		private var chartInfoSet:Boolean;
		private var includeDataSheet:Boolean;
		private var chartHasFilters:Boolean;
		private var chartHasSubCharts:Boolean;
		private var chartHasCustomDatesSubChart:Boolean;
		private var multipleKPIsSelected:Boolean;
		private var dateDataFound:Boolean = false;
		private var dataFound:Boolean = false;
		private var filtersRedrawn:Boolean = false;
		
		// Numbers
		private var lastDayInMonth:Number;
		private var chartMetricAliasKeyIndex:Number = -1;
		private var dateStartIndex:Number = new Number( -1);
		private var dateValueLength:Number = new Number( -1);
		private var a:Number = 0;
		private var z:Number = 0;
		private var cntr:Number = 0;
			
			
		// Dates
		private var startDate:Date = new Date();
		private var endDate:Date = new Date();
		private var actualDate:Date = new Date();
		
			
			
		public function ChartViewer():void 
		{
			super();
			
			// selector
			chartSelectors = new HBox(); // there can be only one,...
			chartSelectors.x = 50;
			chartSelectors.y = 75;
			
			// header
			chartHeader = new HBox(); // there can be only one,...
			chartHeader.percentHeight = 20;
			chartHeader.y = 25;
			chartHeader.x = 50;
			chartHeader.setStyle("horizontalAlign","left");
			
			// styles
			chartStyles = new MultiSelectDropDownCtl();
			chartStyles.dataProvider = [ "AM"]; //"FLEX",// add "XML_SWF", "OPEN_FLASH", "YAHOO",  later
			chartStyles.selectedIndex = 0;
			chartStyles.addEventListener(Event.CLOSE,setChartGenerator);
			
			// displays
			chartDisplays = new MultiSelectDropDownCtl();
			chartDisplays.dataProvider = [ "Column", "Bar", "3D Column", "3D Bar"]; //"Line", "Column_Area"// add "Area", "Bubble", "Pie",  "Axis", "MultiBar", "CandleStick", "Gant", "Doughnut"
			chartDisplays.selectedIndex = 0;
			chartDisplays.addEventListener(Event.CLOSE,setChartDisplay);
			
			// messages
			chartMessages = new TextArea();
			chartMessages.editable = false;
			chartMessages.x = 1224;
			chartMessages.percentWidth = 25;
			chartMessages.percentHeight = 50;
			chartMessages.y = 100;
			
			chartMessagesLabel = new Label();
			chartMessagesLabel.text = "Messages";
			chartMessagesLabel.x = 1224;
			chartMessagesLabel.percentWidth = 25;
			chartMessagesLabel.y = 75;
			chartMessagesLabel.setStyle("fontWeight", "bold");
			
			/* custom kinda */
			customDateRangeLabel = new Label();
			customDateRangeLabel.x = 230;
			customDateRangeLabel.y = 55;
			customDateRangeLabel.text = "Custom Date Range";
			
			chartStartLabel = new Label();
			chartStartLabel.x = 50;
			chartStartLabel.y = 80;
			chartStartLabel.text = "Start";
			chartStartLabel.setStyle("fontWeight", "bold");
			
			chartEndLabel = new Label();
			chartEndLabel.x = 300;
			chartEndLabel.y = 80;
			chartEndLabel.text = "End";
			chartEndLabel.setStyle("fontWeight", "bold");
			
			chartStartChooser = new DateChooser();
			chartStartChooser.x = 525;
			chartStartChooser.y = 50;
			chartStartChooser.visible = false;
			chartStartChooser.yearNavigationEnabled = true;
			//chartStartChooser.addEventListener(MouseEvent.MOUSE_OUT, hideStartDateChooser);
			chartStartChooser.displayedMonth = 0;
			chartStartChooser.addEventListener(CalendarLayoutChangeEvent.CHANGE, showSelectedStartDate);
			
			chartEndChooser = new DateChooser();
			chartEndChooser.x = 755;
			chartEndChooser.y = 50;
			chartEndChooser.visible = false;
			chartEndChooser.yearNavigationEnabled = true;
			//chartEndChooser.addEventListener(MouseEvent.MOUSE_OUT, hideEndDateChooser);
			chartEndChooser.displayedMonth = 0;
			chartEndChooser.addEventListener(CalendarLayoutChangeEvent.CHANGE, showSelectedEndDate);
			
			chartStartDate = new LinkButton();
			chartStartDate.x = 100;
			chartStartDate.y = 80;
			chartStartDate.width = 150;
			chartStartDate.label = new String(chartStartChooser.displayedMonth + 1)+"/01/" + new String(chartStartChooser.displayedYear);
			chartStartDate.setStyle("fontWeight", "bold");
			chartStartDate.addEventListener(MouseEvent.MOUSE_OVER, showStartDateChooser);
			chartStartDate.addEventListener(MouseEvent.CLICK,setChartByCustomDates);
			
			var lastDayInMonth:Number = new Date(chartStartChooser.displayedYear,chartStartChooser.displayedMonth,0).getDate() as Number;
			
			chartEndDate = new LinkButton();
			chartEndDate.x = 350;
			chartEndDate.y = 80;
			chartEndDate.width = 150;
			chartEndDate.label = new String(chartStartChooser.displayedMonth + 1)+"/"+lastDayInMonth+"/"+ new String(chartStartChooser.displayedYear);
			chartEndDate.setStyle("fontWeight", "bold");
			chartEndDate.addEventListener(MouseEvent.MOUSE_OVER, showEndDateChooser);
			chartEndDate.addEventListener(MouseEvent.CLICK,setChartByCustomDates);
			
			chartCustomSubChart = new LinkButton();
			chartCustomSubChart.label = "Use Custom Date Range";
			chartCustomSubChart.x = 225;
			chartCustomSubChart.y = 115;
			chartCustomSubChart.setStyle("fontWeight", "bold");
			chartCustomSubChart.addEventListener(MouseEvent.CLICK,setChartByCustomDates);
			chartCustomSubChart.addEventListener(MouseEvent.MOUSE_OVER, hideEndDateChooser);
			chartCustomSubChart.addEventListener(MouseEvent.MOUSE_OVER, hideStartDateChooser);
			chartCustomSubChart.visible = false;
			
			// objects
			this.chartByLabels = new Array();
			
			// flags
			chartInfoSet = false;
			includeDataSheet = true;
			chartHasFilters = false;
			chartHasSubCharts = false;
			chartHasCustomDatesSubChart = false;
			
			if (stage) 
			{
				init();
			}
			else 
			{
				addEventListener(Event.ADDED_TO_STAGE, init);
			}
		}
		
		public function CreateChart():void
		{
			//this.dbgText.text += "Created Chart:";
			
			
			if (alChart is HTMLChart)
			{
				var htmlAlChart:HTMLChart = alChart as HTMLChart;
				
				//this.dbgText.text += htmlAlChart.Renderer + ":";
				if (this.chartDisplays.selectedIndex < 2)
				{
					htmlAlChart.ThreeDView = false;
				}
				else
				{
					htmlAlChart.ThreeDView = true;
				}
				
				htmlAlChart.Create();
			}
		}
		
		public function DrawChart():void
		{
			var displayObjects:Array;
			
			//this.dbgText.text += "Drew Chart:";
			
			if (alChart is HTMLChart)
			{
				// type the chart
				var htmlAlChart:HTMLChart = alChart as HTMLChart;
				
				// load the chart data string and parse it
				htmlAlChart.LoadChartDataValues();
				
				// draw the panel with chart and details
				htmlAlChart.Draw();
				
				// get the display objects
				displayObjects = htmlAlChart.GetChartDisplayObjects();
				
				// add display objects to viewer
				for (var a:int=0; a < displayObjects.length; a++ )
				{
					// will draw panel and data
					this.addChild(displayObjects[a]);
				}
			}
			
			//this.dbgText.text += "Definately Drew Chart:";
		}
		
		public function SetChartCategories(c:Array):void
		{
			//Alert.show("setting chart cats");
			var a:Number;
			var cVal:String;
			var fVal:String;
			var fields:Array;
			var dataValues:Array;
			var desk:String = "";
			var k:Number;
			
			//this.dbgText.text += "Set Chart Categories:";
			chartByComboBoxes = new Array();
			for (var i:Number = 0; i < c.length;i++ )
			{
				cVal = c[i];
				
				// get ready to draw the subcharts
				if (cVal.indexOf("Chart_By") >= 0)
				{
					this.chartHasSubCharts = true;
					
					//this.dbgText.text += cVal + ":";
					
					//if (this.chartSubCharts == null)
					{
						this.chartSubCharts = new Array();
					}
					
					fields = this.appLoader.GetFieldValues();
					fVal = fields[i];
					
					fVal += "_Values";
					a = this.appLoader.GetChartDataKeys().indexOf(fVal);
					//this.dbgText.text += fVal+ "-"+a+":";
					
					this.chartByComboBox = new MultiSelectDropDownCtl();
					
					if (a >= 0)
					{
						dataValues = this.appLoader.GetChartDataKeyValues()[a].split(",");
							
						for (var j:Number = 0; j < dataValues.length; j++ )
						{
							this.chartSubCharts.push(dataValues[j]);
						}
						
						//this.
						
						this.drawSubChart(i);
					}
					this.chartByComboBox.id = i.toString();
					this.chartByComboBox.dataProvider = chartSubCharts;
					this.chartByComboBox.addEventListener(Event.CLOSE, setComboChartBy);
					//this.chartByComboBox.
					this.chartByComboBoxes.push(chartByComboBox);
				}
				
				// get ready to draw the subcharts
				if (cVal.indexOf("Filter_By") >= 0)
				{
					this.chartHasFilters = true;
					
					//this.dbgText.text += cVal + ":";
					//Alert.show("Showing filter by");
					
					//if (this.chartSubCharts == null)
					{
						this.chartFilters = new Array();
					}
					
					if (this.filterByComboBoxes == null)
					{
						this.filterByComboBoxes = new Array();
					}
					
					fields = this.appLoader.GetFieldValues();
					fVal = fields[i];
					
					fVal += "_Values";
					a = this.appLoader.GetChartDataKeys().indexOf(fVal);
					//this.dbgText.text += fVal+ "-"+a+":";
					
					if (a >= 0)
					{
						dataValues = this.appLoader.GetChartDataKeyValues()[a].split(",");
							
						//HACK!!
						if (dataValues[0] == "ASUPlant")
						{
							desk = "ASU";
							//Alert.show("was hot:"+filterByComboBoxes.length);
						}
						
						if (dataValues[0] == "CoGenPlant")
						{
							desk = "CoGen";
							//Alert.show("was hot:"+filterByComboBoxes.length);
						}
						
						if (dataValues[0] == "All_Plants")
						{
							desk = "All";
							//Alert.show("*");
						}
						
						if ((desk == "CoGen")&&(dataValues[0]!="ASUPlant")&&(dataValues[0]!="CoGenPlant"))
						{
							
							//Alert.show("Cold");
							if (!filtersRedrawn)
							{
								this.chartFilters.push("All_Kpi");
								
								for (k = 0; k < dataValues.length; k++ )
								{
									this.chartFilters.push(dataValues[k]);	
								}
							}
							
						}
						
						if ((desk == "ASU")&&(dataValues[0]!="ASUPlant")&&(dataValues[0]!="CoGenPlant"))
						{
							
							//Alert.show("Hot");
							if (!filtersRedrawn)
							{
								this.chartFilters.push("All_Kpi");
								
								for (k = 0; k < dataValues.length; k++ )
								{
									this.chartFilters.push(dataValues[k]);
								}
							}
						}
						
						if ((desk == "All")&&(dataValues[0]!="ASUPlant")&&(dataValues[0]!="CoGenPlant")&&(dataValues[0]!="All_Plants"))
						{
							if (!filtersRedrawn)
							{
								this.chartFilters.push("All_Kpi");
								//Alert.show("All");
								for (k = 0; k < dataValues.length; k++ )
								{
									this.chartFilters.push(dataValues[k]);
								}
							}
						}
						
						// for the actual field values
						if ((dataValues[0]=="ASUPlant")||(dataValues[0]=="CoGenPlant"))
						{
							this.chartFilters.push("All_Plants");
							//Alert.show("Confusing");
							//if (!filtersRedrawn)
							{
								
								for (k = 0; k < dataValues.length; k++ )
								{
									// add actual desk values
									this.chartFilters.push(dataValues[k]);
								}
							}
							
							desk = "All";
							
						}
						
						//Alert.show("dataVal="+dataValues[0]);
					
						this.addFiltersToHeader();
					}
				}
				
				//Alert.show("cVAl="+cVal);
				
				// get ready to draw the subcharts
				if ((cVal.indexOf("Label_X_By") >= 0))
				{
					fields = this.appLoader.GetFieldValues();
					fVal = fields[i];
				
					fVal += "_Values";
					a = this.appLoader.GetChartDataKeys().indexOf(fVal);
					//this.dbgText.text += fVal+ "-"+a+":";
					
					if (a >= 0)
					{
						this.xLabel = this.appLoader.GetChartDataKeyValues()[a];
					}
				}
				
				// get ready to draw the subcharts
				if ((cVal.indexOf("Label_Y_By") >= 0))
				{
					fields = this.appLoader.GetFieldValues();
					fVal = fields[i];
				
					fVal += "_Values";
					a = this.appLoader.GetChartDataKeys().indexOf(fVal);
					//this.dbgText.text += fVal+ "-"+a+":";
					
					if (a >= 0)
					{
						this.yLabel = this.appLoader.GetChartDataKeyValues()[a];
					}
				}
				
				if((cVal.indexOf("Calculate_By") >= 0))
				{
					fields = this.appLoader.GetFieldValues();
					fVal = fields[i];
				
					fVal += "_Values";
					a = this.appLoader.GetChartDataKeys().indexOf(fVal);
					this.dbgText.text += fVal+ "-"+a+":";
					
					if (a >= 0)
					{
						this.calculationLabel = this.appLoader.GetChartDataKeyValues()[a];
					}
				}
				
			}
			
			// add subCharts to the screen
			//this.addChild(this.chartSelectors);
			
			// draw the header
			this.drawHeader();
			
			// add Header to the chart Viewer
			this.addChild(this.chartHeader);
			
			this.addChild(customDateRangeLabel);
			
			this.addChild(chartStartLabel);
		
			this.addChild(chartEndLabel);
			
			this.addChild(chartStartDate);
		
			this.addChild(chartEndDate);
		
			this.addChild(chartStartChooser);
		
			this.addChild(chartEndChooser);
			
			//this.addChild(this.chartCustomSubChart);
			
		}
		
		public function SetChartData(d:Map ):void
		{
			var key:String = "";
			var aliasKey:String = "";
			var customDateKeys:Array;
			var chartData:String;
			var allChartBySet:Boolean;
			var someChartBySet:Boolean = false;
			var allChartByIndex:Number;
			var someChartByIndex:Number;
			var lastDateChartBySet:Boolean;
			var lastDateChartByIndex:Number;
			var allChartByFlags:Array = new Array();
			var someChartByFlags:Array = new Array();
			var lastChartByFlags:Array = new Array();
			var allChartByData:Array = new Array();
			var someChartByData:Array = new Array();
			var currentDate:Date = new Date();
			var boundaryDate:Date;
			var handlingAlls:Boolean;
			var dict:Dictionary;
			var calcData:Array;
			var allString:String;
			var allCnt:Number = 0;
			var someCnt:Number = 0;
			var usingKeyAliases:Boolean = false;
			var cbv:ComboBox;
			var msddc:MultiSelectDropDownCtl;
			
			dateDataFound = false;
			dataFound = false;
			
			// what are the current chartBys?
			for (var i:Number = 0; i < this.chartByLabels.length; i++ )
			{
				var l:Label = this.chartByLabels[i];
			
				if (i == 0)
				{
					key += l.text;
				
					if (this.chartMetricAliasKeyIndex >= 0)
					{
						//Alert.show(">0");
						usingKeyAliases = true;
					}
					
					if (usingKeyAliases)
					{
						//Alert.show("true");
						if (chartMetricAliasKeyIndex == 0)
						{
							msddc = this.chartByComboBoxes[0] as MultiSelectDropDownCtl;
							cbv = this.chartByComboBoxes[0];
							
							//Alert.show("indices:"+msddc.selectedIndices+" for text:"+l.text);
							if ((msddc.selectedIndices!=null))
							{
								if ((msddc.selectedIndices.length > 1)&&(l.text.indexOf("All")<0))
								{
									aliasKey += "Some_Prdct_Nms"; // HACK!
								}
								else
								{
									aliasKey += this.chartMetricAliasKeys[cbv.selectedIndex];
								}
							}
							else
							{
								
								aliasKey += this.chartMetricAliasKeys[cbv.selectedIndex];
							}
						}
						else
						{
							aliasKey += l.text;
						}
					}
					
					//Alert.show("AliasKey:" + aliasKey);
					//Alert.show("Key:"+key);
				}
				else
				{
					if ((chartHasCustomDatesSubChart)&&(i==2))
					{
						//Alert.show("test1");
						//Alert.show(this.chartStartDate.label.substring(0, this.chartStartDate.label.indexOf("/")));
						//if(this.chartStartDate.text.charAt(0) != this.chartEndDate.text.charAt(0)) // different months
						if(this.chartStartDate.label.substring(0, this.chartStartDate.label.indexOf("/"))!= this.chartEndDate.label.substring(0, this.chartEndDate.label.indexOf("/"))) // different months
						{
							key += "_Last_"; 
						
							if (usingKeyAliases)
							{
								
								aliasKey += "_Last_";
							}
						}
						else
						{
							key += "_" + l.text;
					
							if (usingKeyAliases)
							{
								if (chartMetricAliasKeyIndex == i)
								{
									cbv = this.chartByComboBoxes[chartMetricAliasKeyIndex];
									aliasKey += "_" + this.chartMetricAliasKeys[cbv.selectedIndex];
								}
								else
								{
									aliasKey += "_" + l.text;
								}
							}
						}
					}
					else
					{
						key += "_" + l.text;
					
						if (usingKeyAliases)
						{
							if (chartMetricAliasKeyIndex == i)
							{
								cbv = this.chartByComboBoxes[chartMetricAliasKeyIndex];
								aliasKey += "_" + this.chartMetricAliasKeys[cbv.selectedIndex];
							}
							else
							{
								aliasKey += "_" + l.text;
							}
						}
					}
				}
				//Alert.show("Some chart by flag:"+someChartBySet);
				//Alert.show("Some chart by flag:"+l.text);
				if ((l.text.indexOf("All")>=0)||(l.text.indexOf("Some")>=0))
				{
					if ((l.text.indexOf("All") >= 0)&&(l.text.indexOf("Some")<0))
					{
						// set most recent index
						allChartByIndex = i;
						
						if (!someChartBySet)
						{
							//set flag
							allChartBySet = true;
							someChartBySet = false;
						}
						// save in array for later
						allChartByFlags.push(true);
						someChartByFlags.push(false);
						//Alert.show("just set all to true");
						// count
						allCnt++;
					}
					
					if (l.text.indexOf("Some") >= 0)
					{
						// set most recent index
						someChartByIndex = i;
						
						//if (!allChartBySet)
						{
							//set flag
							someChartBySet = true;
							allChartBySet = false;
						
						
							// save in array for later
							someChartByFlags.push(true);
							allChartByFlags.push(false);
							//Alert.show("just set some to true");
						}
						
						// count
						someCnt++;
						allCnt++;
					}
				}
				else
				{
					allChartByFlags.push(false);
				}
				
				if ((l.text.indexOf("Last")>=0)&&(l.text.indexOf("Mns")>=0))
				{
					// set index
					lastDateChartByIndex = i;
					
					//set date
					lastDateChartBySet = true;
					
					// save in array for later
					lastChartByFlags.push(true);
				}
				else
				{
					lastChartByFlags.push(false);
				}
			}
			
			// filters?
			for (var j:Number = 0; j < this.filterByComboBoxes.length; j++ )
			{
				var cb:ComboBox = this.filterByComboBoxes[j];
			
				{
					key += "_"+cb.selectedItem;
				}
				
				if (usingKeyAliases)
				{
					{
						aliasKey += "_" + cb.selectedItem;
					}
				}
			}
			
			//Alert.show("Key:"+key);
			//Alert.show("aliasKey:"+aliasKey);
			
			var currentKPIMax:Number= 0;
			//Alert.show("currentKey:"+newKey);
			
			if(key.indexOf("Availability")>=0)
			{	
				currentKPIMax = 100;
			}

			if(key.indexOf("Reliability")>=0)
			{	
				currentKPIMax = 20;
			}
			/*
			if(key.indexOf("GEI_O2")>=0)
			{	
				currentKPIMax = 15;
			}
			
			if(key.indexOf("UnPack_N2")>=0)
			{	
				currentKPIMax = 15;
			}
			
			if(key.indexOf("Mesa_savings")>=0)
			{	
				currentKPIMax = 20;
			}
			
			if(key.indexOf("Avg_SCE")>=0)
			{	
				currentKPIMax = 20;
			}
			
			if(key.indexOf("H2_Model")>=0)
			{	
				currentKPIMax = 40;
				//Alert.show("h2model");
			}
			
			if (key.indexOf("Excess_Steam")>=0)
			{
				currentKPIMax = 20;
			}
			*/
			this.alChart.ResetMetricData();
			this.alChart.SetMetricKPIMax(currentKPIMax);
			//Alert.show("key:"+key+"currentMax:"+currentKPIMax);
			
			// add values ending
			var newKeyIndex:String = "";
			var newKeyValueLength:String = "";
			var newAliasKeyIndex:String = ""; 
			var newAliasKeyValueLength:String = "";
			
			// Set up dates for 
			this.setRawDateAndAliasKeys(key, aliasKey);
			
			/* Aquire PIPED DATA from data source map */
			chartData = this.getPipedDataForChart(key, aliasKey, d);
			
			// Handle the results
			if (chartData != null)
			{
				// set the chart string data
				this.alChart.SetData(chartData);
				//this.alChart.SetKPIName();
				//Alert.show(this.chartFilters[1]);
				this.chartMessages.text += "\n:Found data available";
				dataFound = true;
				dateDataFound = true;
			}
			
			/* Aquire DATES W/ KPI DATA from data source map */
			if (! dateDataFound)
			{
				chartData = getDateWKPI_DataForChart(key, aliasKey, d);
				
				if (chartData != null)
				{
					this.chartMessages.text += "\n:Found data available for " + key;
					dateDataFound = true;
				}
				else
				{
					this.chartMessages.text += "\n:There is no data available for " + key;
					dateDataFound = false;
				}
			}
			
			/* Aqcuire DATES W/O KPI DATA from data source map */
			if (!dateDataFound)
			{
				chartData = getDateWOKPI_DataForChart(key, aliasKey, d);
				
				if (chartData != null)
				{
					this.chartMessages.text += "\n:Found data available for " + key;
					dateDataFound = true;
					//Alert.show("data");
				}
				else
				{
					this.chartMessages.text += "\n:There is no data available for " + key;
					dateDataFound = false;
					//Alert.show("No data");
				}
			}
			
			/* Aqcuire METRICS W/ MONTH from data source map */
			chartData = this.getMetricWMon_DataForChart(key, aliasKey, d);
			
			if (chartData != null)
			{
				this.chartMessages.text += "\n:Found data available for " + newAliasKey;
				dataFound = true;
			}
			else
			{
				this.chartMessages.text += "\n:There is no data available for " + newKey;
				
				if ((newKey.indexOf("Last_") < 0) && (newKey.indexOf("All_") < 0) && (!chartHasCustomDatesSubChart)&&(newAliasKey.indexOf("Last_") < 0) && (newAliasKey.indexOf("All_") < 0))
				{
					if ((!dataFound)||(!dateDataFound))
					{
						//Alert.show("There Are No Data Sets Available for Selection(-3)");
					}
				}
				
			}
			
			/* Aqcuire METRICS W/O MONTH from data source map */
			chartData = this.getMetricWOMon_DataForChart(key, aliasKey,d);
			//Alert.show("key:"+aliasKey+"-chartData:"+chartData+"=dateDataFound?"+dateDataFound);
			if ((chartData != null)&&(dateDataFound))
			{
				//Alert.show("found data!");
				if (this.multipleKPIsSelected)
				{
					this.alChart.SetCategoryAxisValues(chartData);
					//this.alChart.SetCategory(tempDict.Chart_By);
					//this.alChart.SetKPIName();
					//Alert.show(this.chartFilters[1]);
				
				}
				
				this.chartMessages.text += "\n:Found data available for " + newAliasKey;
				dataFound = true;
			}
			else
			{
				this.chartMessages.text += "\n:There is no data available for " + newKey;
				//Alert.show("key?:"+key);
				if ((key.indexOf("Last_") < 0)  && (newKey.indexOf("Some_") < 0) && (newKey.indexOf("All_") < 0) && (!chartHasCustomDatesSubChart)&&(aliasKey.indexOf("Last_") < 0) && (newAliasKey.indexOf("All_") < 0))
				{
					if ((!dateDataFound)||(!dataFound))
					{
						//Alert.show("There Are No Data Sets Available for Selection(-3)");
						//this.alChart.ResetMetricData(); // reset Metric Data
					}
				}
			}
			
			if ((key.indexOf("All_")<0))
			{
				this.chartEndDate.visible = true;
				this.chartStartDate.visible = true;
				this.chartStartLabel.visible = true;
				this.chartEndLabel.visible = true;
				//this.chartCustomSubChart.visible = true;	
			}
			
			//Alert.show(" key:" + key);
			//Alert.show(" alias:" + aliasKey);
			//Alert.show("regular key:" + newKey);
			//Alert.show("alias key:"+newAliasKey);
			//this.dbgText.text = "metric data set:"+this.alChart.GetData();
			
			if ((someChartBySet||allChartBySet||lastDateChartBySet||chartHasCustomDatesSubChart)&&((key.indexOf("Some")>=0)||(key.indexOf("All")>=0)||(key.indexOf("Last")>=0)))
			{
				
				/*
				this.chartEndDate.visible = false;
				this.chartStartDate.visible = false;
				this.chartStartLabel.visible = false;
				this.chartEndLabel.visible = false;
				this.chartCustomSubChart.visible = false;	
				this.chartEndChooser.visible = false;
				this.chartStartChooser.visible = false;
				this.customDateRangeLabel.visible = false;
				*/
				this.alChart.ResetCategoryData();
				
				
				this.chartMessages.text += "\n:No static data found, aggregating current data";
				//Alert.show("Aggregating current data"); //START!
				
				// handle alls - saving back to 
				if(allChartBySet||someChartBySet)
				{
					var all_d:Map;
					var all_calcData:Array;
					var allCategory:String;
					var allCategoryStart:String;
					var allCategories:String;
					var allChartByCategories:Array;
					var allCatName:String;
					var allCatAliasName:String;
					var allTmpData:String;
					var allTmpKeys:String;
					var allTmpDateKeys:String;
					var h:Number;
					var tempKeyIndex:String;
					var tempKeyIndexLength:String;
					var tempAliasKeyIndex:String;
					var tempAliasKeyIndexLength:String;
					var tmpCatArray:Array;
					
					all_d = new Map();
					
					
					handlingAlls = true;
					//dateDataFound = false;
					//this.dbgText.text = "-allChartByIndex:" + allChartByIndex;
					//Alert.show("chartByLabels:"+this.chartByLabels[0].text+","+this.chartByLabels[1].text+":"+this.chartByLabels.length);
					for (h = this.chartByLabels.length-1; h >= 0; h-- )
					{
						//**-->
						
						
						//this.dbgText.text = "-"+h+"-this is the flag:" + allChartByFlags[h];
						//Alert.show( "-"+h+"-this is the flag:" + allChartByFlags[h]);
						if ((allChartByFlags[h]||someChartByFlags[h]))
						{
							//Alert.show("Some chart by flag:"+someChartBySet+"h="+h);
							// get All category
							allString = this.chartByLabels[h].text;// 
							//Alert.show("allStr:"+allString)
							if (!someChartBySet)
							{
								allCategory = allString.substring(allString.indexOf("All_") + 4, allString.length -1);
							}
							else
							{
								allCategory = allString.substring(allString.indexOf("Some_") + 5, allString.length -1);
								
							}
							// first All encountered in chartr by flags
								
							//this.dbgText.text += "\n-this is the cat:" + allCategory;
							//Alert.show("allCat:"+allCategory);
							
							// get field for index
							var dataKeyIndex:Number = appLoader.GetChartDataKeys().indexOf(allCategory + "_Values");
							allCategories = appLoader.GetChartDataKeyValues()[dataKeyIndex];
							
							//this.dbgText.text += "\n-these are the cats:" + allCategories;
							
							// create array 
							allChartByCategories = new Array();
							//Alert.show("all cats"+allCategories+":"+allCategory + "_Values");
							if (allCategories != null)
							{
								allChartByCategories = allCategories.split(",");
							}
							//Alert.show("AllChartByCats:"+allChartByCategories+":"+chartMetricAliasKeys);
							calcData = new Array();
							
							// create a map/dict for the fields
							for (i = 0; i < allChartByCategories.length;i++ )
							{
								dict = new Dictionary();
								
								//this.dbgText.text += "\n-these are the cats:" + allChartByCategories[i];
								//Alert.show("category:"+allChartByCategories[i]);
								if (allChartByCategories[i].indexOf("All") < 0)
								{
									if (chartMetricAliasKeys[i] != null )
									{
										dict["Chart_By_Alias"] = new String(chartMetricAliasKeys[i]);
									}
									else
									{
										dict["Chart_By_Alias"] = new String(chartMetricAliasKeys[i]);
									}
									dict["Chart_By"] = new String(allChartByCategories[i]); // 
									dict["Data"] = new String("") // copy of data
									dict["Dates"] = new String("") // copy of dates
									dict["Metrics"] = new String("") // copy of metrics
									dict["Labels"] = new String("") // what shows on the chart for values
									dict["Frequency"] = new Number(0); // frequency of month
									dict["Calculation"] = new String("Average"); // Average? hard coded for now
									dict["DateIndexes"] = new Array();
									dict["DateStartIndex"] = new String();
									dict["DateValueLength"] = new String();
									
									if ((!someChartBySet)&&(allChartBySet))
									{
										// add to dataSet
										calcData.push(dict);
									}
									else
									{
										if ((someChartBySet) && (!allChartBySet))
										{
											var mdd:MultiSelectDropDownCtl = this.chartByComboBoxes[h] as MultiSelectDropDownCtl;
											var selectedCategories:String = mdd.selectedItems as String;
											
											//Alert.show("CurrentCategory:"+allChartByCategories[i]+" AND SelectedItems:" + mdd.selectedItems);
											if (mdd.selectedItems.indexOf(allChartByCategories[i]) >= 0)
											{
												calcData.push(dict);
											}
											else
											{
												if (!someChartByFlags[h])
												{
													calcData.push(dict);
												}
											}
										}
									}
								}
							}
					
							//this.dbgText.text += "\n" +key + "_Metric_Values";
							//Alert.show("calc data len:"+calcData.length);
							
							// load the data and save frequency data
							for ( i= 0; i < calcData.length;i++ )
							{
								dateDataFound = false; // reset each time
								
								tempDict = calcData[i];
								tempFreq = tempDict.Frequency;
								allCatName = tempDict["Chart_By"];
								allCatAliasName = tempDict["Chart_By_Alias"];
								
								//Alert.show("ak:"+aliasKey);
								if (h!=this.chartMetricAliasKeyIndex)// works for 1st All
								{
									allCatAliasName = allCatName;
									//Alert.show("yes["+h+"]:"+allCatAliasName);
								}
								else 
								{
									//Alert.show("no:"+allCatAliasName);
								}
								
								// if this is a piped one
								tempKey = key.substring(0, key.indexOf("All_")) + allCatName + key.substring(key.indexOf("_"+allCategory) + allCategory.length+2) + "_Values";
								tempAliasKey = aliasKey.substring(0, aliasKey.indexOf("All_")) + allCatAliasName + aliasKey.substring(aliasKey.indexOf("_"+allCategory) + allCategory.length+2) + "_Values";
								//this.dbgText.text += "TK-Values:" + tempKey + "\n";
								
								//Alert.show("-"+tempKey);
								//Alert.show("--"+tempAliasKey);
								if (someChartBySet && tempKey.indexOf("All")>=0)
								{
									tempKey = allCatName + tempKey.substring(tempKey.indexOf("Some_Prdct_Nms")+15);
									tempAliasKey = allCatAliasName + tempAliasKey.substring(tempAliasKey.indexOf("Some_Prdct_Nms")+15);
								}
								
								//Alert.show("+"+tempKey);
								//Alert.show("++"+tempAliasKey);
								
								chartData = d.getValue(tempKey);
								
								if (chartData != null)
								{
									dataFound = true;
									dateDataFound = true;
									tempDict.Data = chartData;
									//Alert.show("found1!!");
									
								}
								else
								{
									chartData = d.getValue(tempAliasKey);
								
									if (chartData != null)
									{
										dataFound = true;
										dateDataFound = true;
										tempDict.Data = chartData;
										//Alert.show("found2!!");
									}
								}
							
								//Alert.show("thedateFound="+dateDataFound);
								
								// DATE W/ KPI
								tempKey = key.substring(0, key.indexOf("All_")) + allCatName + key.substring(key.indexOf("_"+allCategory) + allCategory.length+2) + "_Date_Values";
								tempAliasKey = aliasKey.substring(0, aliasKey.indexOf("All_")) + allCatAliasName + aliasKey.substring(aliasKey.indexOf("_"+allCategory) + allCategory.length+2) + "_Date_Values";
								//this.dbgText.text += "TK-dates:" + tempKey + "\n";
								
								if (someChartBySet && tempKey.indexOf("All")>=0)
								{
									tempKey = tempKey.substring(tempKey.indexOf("Some_Prdct_Nms")+15);
									tempAliasKey =  tempAliasKey.substring(tempAliasKey.indexOf("Some_Prdct_Nms")+15);
								}
								
								//Alert.show("!"+tempKey);
								//Alert.show("!!"+tempAliasKey);
								
								chartData = d.getValue(tempKey);
								//Alert.show("At the dates key");
								if (chartData != null)
								{
									// set the chart string data
									if (!this.chartHasCustomDatesSubChart)
									{
										dateDataFound = true;
										tempDict.Dates = chartData;
										//Alert.show("regular dates with data");
										this.alChart.SetCategoryAxisDates(tempDict.Dates);
										//this.alChart.SetCategory(tempDict.Chart_By);									
										
									}
									else
									{
										//Alert.show("custom dates with data");
								
										startDate = new Date();
										startDate.setTime(Date.parse(this.chartStartDate.label));
										
										endDate = new Date();
										endDate.setTime(Date.parse(this.chartEndDate.label));
										
										actualDate = new Date();
										allPossibleDates = chartData.split(',');
										newChartData = "";
										dateIndexes = new Array();
										
										for (a = 0; a < allPossibleDates.length; a++ )
										{
											date = allPossibleDates[a] as String;
											if (date != null)
											{
												//Alert.show(date+":"+startDate.toString()+":"+endDate.toString());
												try
												{
													actualDate.setTime(Date.parse(date));
													
													// is it in the range?
													if ((actualDate.getTime() >= startDate.getTime()) && (actualDate.getTime() <= endDate.getTime()))
													{
														newChartData += allPossibleDates[a] + ",";
														dateIndexes.push(a);
														//Alert.show("this is a:"+a);
													}
												}
												catch (err:Error)
												{
													Alert.show("[ERROR] problems loading custom dates:"+err.message);
												}
											}
										}
										
										//dataFound = true;
										dateDataFound = true;
										tempDict.Dates = newChartData;
										tempDict.DateIndexes = dateIndexes;
										//Alert.show("DateIndexes going in:"+tempDict.DateIndexes);
										this.alChart.SetCategoryAxisDates(tempDict.Dates);
										//this.alChart.SetCategory(tempDict.Chart_By);								
									}
								}
								else
								{
									
									chartData = d.getValue(tempAliasKey);
									//Alert.show("At the dates key");
									if (chartData != null)
									{
										// set the chart string data
										if (!this.chartHasCustomDatesSubChart)
										{
											dateDataFound = true;
											tempDict.Dates = chartData;
											//Alert.show("regular dates with alias data");
											this.alChart.SetCategoryAxisDates(tempDict.Dates);
											//this.alChart.SetCategory(tempDict.Chart_By);								
											
										}
										else
										{
											//Alert.show("custom dates with data");
									
											startDate = new Date();
											startDate.setTime(Date.parse(this.chartStartDate.label));
											
											endDate = new Date();
											endDate.setTime(Date.parse(this.chartEndDate.label));
											
											actualDate = new Date();
											allPossibleDates = chartData.split(',');
											newChartData = "";
											dateIndexes = new Array();
											
											for (a = 0; a < allPossibleDates.length; a++ )
											{
												date = allPossibleDates[a] as String;
												if (date != null)
												{
													//Alert.show(date+":"+startDate.toString()+":"+endDate.toString());
													try
													{
														actualDate.setTime(Date.parse(date));
														
														// is it in the range?
														if ((actualDate.getTime() >= startDate.getTime()) && (actualDate.getTime() <= endDate.getTime()))
														{
															newChartData += allPossibleDates[a] + ",";
															dateIndexes.push(a);
															//Alert.show("this is a:"+a);
														}
													}
													catch (err:Error)
													{
														Alert.show("[ERROR] problems loading custom dates:"+err.message);
													}
												}
											}
											
											//dataFound = true;
											dateDataFound = true;
											tempDict.Dates = newChartData;
											tempDict.DateIndexes = dateIndexes;
											//Alert.show("DateIndexes going in:"+tempDict.DateIndexes);
											this.alChart.SetCategoryAxisDates(tempDict.Dates);
											//this.alChart.SetCategory(tempDict.Chart_By);									
										}
										
									}
									else
									{
										if ((key.indexOf("Last_") >= 0)||(allCnt>1))
										{
											if (all_d.getValue(key + "_Date_Values")!=null)
											{
												allTmpDateKeys = all_d.getValue(key + "_Date_Values");
												
												//if (allTmpKeys.indexOf("|") >= 0)
												if(allTmpDateKeys.indexOf(allCatName)<0)
												{
													allTmpDateKeys = allCatName + "," + allTmpDateKeys;
												}
												
											}
											else
											{
												if (allTmpDateKeys != null)
												{
													allTmpDateKeys += allCatName;
												}
												else
												{
													allTmpDateKeys = allCatName;
												}
											}
											
											all_d.setValue(key+"_Date_Values",allTmpDateKeys);
										}
										else
										{
											if ((aliasKey.indexOf("Last_") >= 0)||(allCnt>1))
											{
												if (all_d.getValue(aliasKey + "_Date_Values")!=null)
												{
													allTmpDateKeys = all_d.getValue(aliasKey + "_Date_Values");
													//Alert.show(allCatAliasName);
													//if (allTmpKeys.indexOf("|") >= 0)
													if(allTmpDateKeys.indexOf(allCatAliasName)<0)
													{
														allTmpDateKeys = allCatAliasName + "," + allTmpDateKeys;
													}
													
												}
												else
												{
													if (allTmpDateKeys != null)
													{
														allTmpDateKeys += allCatAliasName;
													}
													else
													{
														allTmpDateKeys = allCatAliasName;
													}
												}
												
												all_d.setValue(aliasKey+"_Date_Values",allTmpDateKeys);
											}
										}
									}
								}
								
								//Alert.show("ddf:"+dateDataFound);
								
								if (!dateDataFound)
								{
									// DATE W/O KPI
									cbs = this.filterByComboBoxes[1];
									tempKey = key.substring(0, key.indexOf("All_")) + allCatName + key.substring(key.indexOf("_" + allCategory) + allCategory.length + 2);// + "_Date_Values";
									tempKey = tempKey.substring(0, tempKey.indexOf(cbs.selectedLabel)) +  "Date_Values";
									
									tempAliasKey = aliasKey.substring(0, aliasKey.indexOf("All_")) + allCatAliasName + aliasKey.substring(aliasKey.indexOf("_"+allCategory) + allCategory.length+2)// + "_Date_Values";
									tempAliasKey = tempAliasKey.substring(0, tempAliasKey.indexOf(cbs.selectedLabel)) + "Date_Values";
									//this.dbgText.text += "TK-dates:" + tempKey + "\n";
									
									//Alert.show("$$"+tempKey);
									if (someChartBySet && tempKey.indexOf("All")>=0)
									{
										tempKey =  tempKey.substring(tempKey.indexOf("Some_Prdct_Nms")+15);
										tempAliasKey =  tempAliasKey.substring(tempAliasKey.indexOf("Some_Prdct_Nms")+15);
									}
									
									
									tempKeyIndex = tempKey + "_Index";
									tempKeyIndexLength = tempKey + "_Length";
									
									tempAliasKeyIndex = tempAliasKey + "_Index";
									tempAliasKeyIndexLength = tempAliasKey + "_Length";
									
									//Alert.show("$"+tempKey);
									//Alert.show("$$"+tempAliasKey);
									
									chartData = d.getValue(tempKey);
									//Alert.show("At the dates key");
									//Alert.show("Chart Data:"+chartData);
									if (chartData != null)
									{
										//Alert.show("chartBy:"+tempDict.Chart_By);
										// set the chart string data
										if (!this.chartHasCustomDatesSubChart)
										{
											dateDataFound = true;
											//tempDict.Dates = chartData;
											//Alert.show("regular dates with data");
											tempDict.Dates = chartData;
											randomArray = chartData.split(',');
											randomArray.reverse();
											//this.alChart.SetCategoryAxisDates(randomArray.join(','));
											this.alChart.SetCategoryAxisDates(tempDict.Dates);
											//this.alChart.SetCategory(tempDict.Chart_By);									
											
											//Alert.show("indx:len="+tempDateKeyIndex+":"+tempDateKeyIndexLength);
											if (d.getValue(tempKeyIndex) != null)
											{
												tempDict.DateStartIndex = d.getValue(tempKeyIndex);
												//Alert.show(tempDateKeyIndex+":"+d.getValue(tempDateKeyIndex));
											}
											else
											{
												tempDict.DateStartIndex = d.getValue(tempAliasKeyIndex);
												//Alert.show(tempDateAliasKeyIndex+":"+d.getValue(tempDateAliasKeyIndex));
											}
											
											if (d.getValue(tempKeyIndexLength) != null)
											{
												tempDict.DateValueLength = d.getValue(tempKeyIndexLength);
											}
											else
											{
												tempDict.DateValueLength = d.getValue(tempAliasKeyIndexLength);
											}
											//Alert.show("indx:len="+tempDict.DateStartIndex+":"+tempDict.DateValueLength);
											//Alert.show(chartData);
										}
										else
										{
											//Alert.show("custom dates with data");
									
											startDate = new Date();
											startDate.setTime(Date.parse(this.chartStartDate.label));
											
											endDate = new Date();
											endDate.setTime(Date.parse(this.chartEndDate.label));
											
											actualDate = new Date();
											allPossibleDates = chartData.split(',');
											newChartData = "";
											dateIndexes = new Array();
											
											for (a = 0; a < allPossibleDates.length; a++ )
											{
												date = allPossibleDates[a] as String;
												if (date != null)
												{
													//Alert.show(date+":"+startDate.toString()+":"+endDate.toString());
													try
													{
														actualDate.setTime(Date.parse(date));
														
														// is it in the range?
														if ((actualDate.getTime() >= startDate.getTime()) && (actualDate.getTime() <= endDate.getTime()))
														{
															newChartData += allPossibleDates[a] + ",";
															dateIndexes.push(a);
															//Alert.show("this is a:"+a);
														}
													}
													catch (err:Error)
													{
														Alert.show("[ERROR] problems loading custom dates:"+err.message);
													}
												}
											}
											
											//dataFound = true;
											dateDataFound = true;
											tempDict.Dates = newChartData;
											tempDict.DateIndexes = dateIndexes;
											//Alert.show("DateIndexes going in:"+tempDict.DateIndexes);
											this.alChart.SetCategoryAxisDates(tempDict.Dates);
											//this.alChart.SetCategory(tempDict.Chart_By);								
										}
										
									}
									else
									{
										
										chartData = d.getValue(tempAliasKey);
										//Alert.show("At the dates key:"+tempAliasKey);
										//Alert.show("Alias Chart Data:"+chartData);
										if (chartData != null)
										{
											
											//Alert.show(tempAliasKeyIndex+":"+d.getValue(tempAliasKeyIndex));
											//Alert.show("not Null:"+tempAliasKey);
											// set the chart string data
											if (!this.chartHasCustomDatesSubChart)
											{
												dateDataFound = true;
												//tempDict.Dates = chartData;
												//Alert.show("regular dates with data:"+chartData);
												
												tempDict.Dates = chartData;
												randomArray = chartData.split(',');
												randomArray.reverse();
												//Alert.show("regular dates with data:"+randomArray.join(','));
												
												//this.alChart.SetCategoryAxisDates(randomArray.join(','));
												this.alChart.SetCategoryAxisDates(tempDict.Dates);
												//this.alChart.SetCategory(tempDict.Chart_By);
												//Alert.show("indx:len="+tempAliasKeyIndex+":"+tempAliasKeyIndexLength);
												if (d.getValue(tempKeyIndex) != null)
												{
													tempDict.DateStartIndex = d.getValue(tempKeyIndex);
													//Alert.show(tempDateKeyIndex+":"+d.getValue(tempDateKeyIndex));
												}
												else
												{
													tempDict.DateStartIndex = d.getValue(tempAliasKeyIndex);
													//Alert.show(tempAliasKeyIndex+":"+d.getValue(tempAliasKeyIndex));
												}
												
												if (d.getValue(tempKeyIndexLength) != null)
												{
													tempDict.DateValueLength = d.getValue(tempKeyIndexLength);
												}
												else
												{
													tempDict.DateValueLength = d.getValue(tempAliasKeyIndexLength);
												}
												//Alert.show(chartData);
												//Alert.show("indx:len="+tempDict.DateStartIndex+":"+tempDict.DateValueLength);
												
											}
											else
											{
												//Alert.show("custom dates with data");
										
												startDate = new Date();
												startDate.setTime(Date.parse(this.chartStartDate.label));
												
												endDate = new Date();
												endDate.setTime(Date.parse(this.chartEndDate.label));
												
												actualDate = new Date();
												allPossibleDates = chartData.split(',');
												newChartData = "";
												dateIndexes = new Array();
												
												var bIndx:Number = new Number(d.getValue(tempAliasKeyIndex));
												
												for (a = 0; a < allPossibleDates.length; a++ )
												{
													date = allPossibleDates[a] as String;
													if (date != null)
													{
														//Alert.show(date+":"+startDate.toString()+":"+endDate.toString());
														try
														{
															actualDate.setTime(Date.parse(date));
															
															// is it in the range?
															//Alert.show("actual Dtae:"+actualDate.toString());
															if ((actualDate.getTime() >= startDate.getTime()) && (actualDate.getTime() <= endDate.getTime()))
															{
																newChartData += allPossibleDates[a] + ",";
																dateIndexes.push(a+bIndx);
																//Alert.show("this is a:"+a);
															}
														}
														catch (err:Error)
														{
															Alert.show("[ERROR] problems loading custom dates:"+err.message);
														}
													}
												}
												
												//dataFound = true;
												dateDataFound = true;
												tempDict.Dates = newChartData;
												tempDict.DateIndexes = dateIndexes;
												//Alert.show("DateIndexes going in:"+tempDict.DateIndexes);
												this.alChart.SetCategoryAxisDates(tempDict.Dates);
												//this.alChart.SetCategory(tempDict.Chart_By);
												//Alert.show("Dates Inbolv3d:"+tempDict.Dates)
																				
											}
											
										}
										else
										{
											//Alert.show("Last_All_Null:"+allCatName+","+allCatAliasName);
											if ((key.indexOf("Last_") >= 0)||(allCnt>1))
											{
												//Alert.show("yup");
												if (all_d.getValue(key + "_Date_Values")!=null)
												{
													allTmpDateKeys = all_d.getValue(key + "_Date_Values");
													
													//if (allTmpKeys.indexOf("|") >= 0)
													if(allTmpDateKeys.indexOf(allCatName)<0)
													{
														allTmpDateKeys = allCatName + "," + allTmpDateKeys;
													}
													//Alert.show(allTmpDateKeys);
												}
												else
												{
													if (allTmpDateKeys != null)
													{
														allTmpDateKeys += allCatName;
													}
													else
													{
														allTmpDateKeys = allCatName;
													}
												}
												
												all_d.setValue(key+"_Date_Values",allTmpDateKeys);
											}
											
											if(tempDict["Chart_By_Alias"]==allCatAliasName)
											{
												//Alert.show("we hit here:"+aliasKey);
												if ((aliasKey.indexOf("Last_") >= 0)||(allCnt>1))
												{
													//Alert.show("yupyup:"+aliasKey);
													if (all_d.getValue(aliasKey + "_Date_Values")!=null)
													{
														allTmpDateKeys = all_d.getValue(aliasKey + "_Date_Values");
														
														//if (allTmpKeys.indexOf("|") >= 0)
														if(allTmpDateKeys.indexOf(allCatAliasName)<0)
														{
															allTmpDateKeys = allCatAliasName + "," + allTmpDateKeys;
														}
														//Alert.show(allTmpDateKeys);
													}
													else
													{
														if (allTmpDateKeys != null)
														{
															allTmpDateKeys += allCatAliasName;
														}
														else
														{
															allTmpDateKeys = allCatAliasName;
														}
													}
													
													all_d.setValue(aliasKey+"_Date_Values",allTmpDateKeys);
												}
											}
										}
									}
								}
								//Alert.show("chartBy:"+tempDict.Chart_By);
								// METRIC W/ MONTH
								tempKey = key.substring(0, key.indexOf("All_")) + allCatName + key.substring(key.indexOf("_"+allCategory) + allCategory.length+2) + "_Metric_Values";
								tempAliasKey = aliasKey.substring(0, aliasKey.indexOf("All_")) + allCatAliasName + aliasKey.substring(aliasKey.indexOf("_"+allCategory) + allCategory.length+2) + "_Metric_Values";
								//this.dbgText.text += "\nTK-Metrics:" + tempKey;
								
								if (someChartBySet && tempKey.indexOf("All")>=0)
								{
									tempKey = tempKey.substring(tempKey.indexOf("Some_Prdct_Nms")+15);
									tempAliasKey = tempAliasKey.substring(tempAliasKey.indexOf("Some_Prdct_Nms")+15);
								}
								
								//Alert.show("*"+tempKey);
								//Alert.show("**"+tempAliasKey);
								
								chartData = d.getValue(tempKey);
								//Alert.show("Metric Data:"+chartData);
								if ((chartData != null)&&(dateDataFound))
								{
									
									
									if (!this.chartHasCustomDatesSubChart)
									{
										dataFound = true;
										tempDict.Metrics = chartData;
										tempFreq++;
										tempDict.Frequency = tempFreq;
										//Alert.show("regular chart data(2)");
										tmpCatArray = tempDict.Metrics.split(",");
										this.alChart.SetCategoryAxisValues(tmpCatArray.reverse().join());
										
										this.alChart.SetCategory(tempDict.Chart_By);
									}
									else
									{
										//Alert.show("custom chart data(2)");
										newChartData = "";
										allPossibleMetrics = chartData.split(',');
										
										if (dateIndexes.length == 0 )
										{
											//Alert.show("There Are No Data Sets Available For Selection(2)");
										}
										else
										{
											for (a = dateIndexes[0]; a <= dateIndexes.length+1; a++ )
											{
												//Alert.show("this is allPossibleMetrics:" + allPossibleMetrics[a]+"and a="+a);
												
												if ((allPossibleMetrics[a] != null)&&(dateIndexes[a-dateIndexes[0]]!=null))
												{
													newChartData += allPossibleMetrics[a] + ",";	
													//Alert.show("this is newChartData:"+newChartData);
												}
											}
										}
										dataFound = true;
										tempDict.Metrics = newChartData;
										tempFreq++;
										tempDict.Frequency = tempFreq;
										tmpCatArray = tempDict.Metrics.split(",");
										this.alChart.SetCategoryAxisValues(tmpCatArray.reverse().join());
										this.alChart.SetCategory(tempDict.Chart_By);
										//this.alChart.SetCategoryAxisValues(tempDict.Metrics);
									}
									

								}
								else
								{	
									chartData = d.getValue(tempAliasKey);
									//Alert.show("in with the alias:"+tempAliasKey+","+tempKey+","+aliasKey);
									//Alert.show("Alias Metric Data:" + chartData);
									//this.alChart.SetCategoryAxisValues(chartData);
									if ((chartData != null)&&(dateDataFound))
									{
										//this.alChart.SetCategoryAxisValues(chartData);
									
										if (!this.chartHasCustomDatesSubChart)
										{
											dataFound = true;
											tempDict.Metrics = chartData;
											tempFreq++;
											tempDict.Frequency = tempFreq;
											//Alert.show("regular chart data(2)");
											tmpCatArray = tempDict.Metrics.split(",");
											this.alChart.SetCategoryAxisValues(tmpCatArray.reverse().join());
											this.alChart.SetCategory(tempDict.Chart_By);
											//this.alChart.SetCategoryAxisValues(tempDict.Metrics);
											//Alert.show("Cat Value:"+tempDict.Chart_By);
										}
										else
										{
											//Alert.show("custom chart data(2)");
											newChartData = "";
											allPossibleMetrics = chartData.split(',');
											
											if (dateIndexes.length == 0 )
											{
												//Alert.show("There Are No Data Sets Available For Selection(2)");
											}
											else
											{
												for (a = dateIndexes[0]; a <= dateIndexes.length+1; a++ )
												{
													//Alert.show("this is allPossibleMetrics:" + allPossibleMetrics[a]+"and a="+a);
													
													if ((allPossibleMetrics[a] != null)&&(dateIndexes[a-dateIndexes[0]]!=null))
													{
														newChartData += allPossibleMetrics[a] + ",";	
														//Alert.show("this is newChartData:"+newChartData);
													}
												}
											}
											dataFound = true;
											tempDict.Metrics = newChartData;
											tempFreq++;
											tempDict.Frequency = tempFreq;
											tmpCatArray = tempDict.Metrics.split(",");
											//this.alChart.SetCategoryAxisValues(tmpCatArray.reverse().join());
											this.alChart.SetCategory(tempDict.Chart_By);
											this.alChart.SetCategoryAxisValues(tempDict.Metrics);
											//Alert.show("data here!");
										}

									}
									else
									{
										//this.dbgText.text = "\nNOdata";
										if ((key.indexOf("Last_") >= 0))
										{
											if (all_d.getValue(key + "_Metric_Values")!=null)
											{
												allTmpKeys = all_d.getValue(key + "_Metric_Values");
												
												//Alert.show(allTmpKeys);
												//if (allTmpKeys.indexOf("|") >= 0)
												if((allTmpKeys.indexOf(allCatName)<0))
												{
													allTmpKeys = allCatName + "," + allTmpKeys;
												}
												
											}
											else
											{
												//this.dbgText.text += "\nwz!thr=" + allTmpKeys;
												if (allTmpKeys != null)
												{
													allTmpKeys += allCatName;
												}
												else
												{
													allTmpKeys = allCatName;
												}
											}
											
											
											all_d.setValue(key + "_Metric_Values", allTmpKeys);
											//this.dbgText.text +="\n+key wz =>"+allTmpKeys+":"+key + "_Metric_Values";
										}
										
										if(tempDict["Chart_By_Alias"]==allCatAliasName)
										{
											if ((aliasKey.indexOf("Last_") >= 0))
											{
												if (all_d.getValue(aliasKey + "_Metric_Values")!=null)
												{
													allTmpKeys = all_d.getValue(aliasKey + "_Metric_Values");
													
													//Alert.show(allTmpKeys);
													if((allTmpKeys.indexOf(allCatAliasName)<0))
													{
														allTmpKeys = allCatAliasName + "," + allTmpKeys;
													}
													
												}
												else
												{
													//this.dbgText.text += "\nwz!thr=" + allTmpKeys;
													if (allTmpKeys != null)
													{
														allTmpKeys += allCatAliasName;
													}
													else
													{
														allTmpKeys = allCatAliasName;
													}
												}
												
												
												all_d.setValue(aliasKey + "_Metric_Values", allTmpKeys);
												//this.dbgText.text +="\n+key wz =>"+allTmpKeys+":"+key + "_Metric_Values";
											}
										}
									}
									
								}
							
								// save data back to dict
								calcData[i] = tempDict;
								
								//if (!dataFound)
								{
									// METRIC W/O MONTH
									tempKey = rawDatesKey.substring(0, rawDatesKey.indexOf("All_")) + allCatName + rawDatesKey.substring(key.indexOf("_"+allCategory) + allCategory.length+2) + "_Metric_Values";
									tempAliasKey = rawDatesAliasKey.substring(0, rawDatesAliasKey.indexOf("All_")) + allCatAliasName + rawDatesAliasKey.substring(rawDatesAliasKey.indexOf("_"+allCategory) + allCategory.length+2) + "_Metric_Values";
									//this.dbgText.text += "\nTK-Metrics:" + tempKey;
									
									if (someChartBySet && tempKey.indexOf("All")>=0)
									{
										//Alert.show("handling some ops");
										tempKey = tempKey.substring(tempKey.indexOf("Some_Prdct_Nms")+15);
										tempAliasKey =  tempAliasKey.substring(tempAliasKey.indexOf("Some_Prdct_Nms")+15);
									}
									
									//Alert.show("@"+tempKey);
									//Alert.show("@@"+tempAliasKey);
									chartData = d.getValue(tempKey);
									//Alert.show("No Mon Metric Data:" + chartData);
									if ((chartData != null)&&(dateDataFound))
									{
										//this.alChart.SetCategoryAxisValues(chartData);
									
										if (!this.chartHasCustomDatesSubChart)
										{
											dataFound = true;
											tempFreq++;
											tempDict.Frequency = tempFreq;
											//Alert.show("regular chart data(2)");
											if (tempDict.DateStartIndex != null)
											{
												validMetrics = d.getValue(tempKey).split(",");
												cntr = new Number(0);
												dateStartIndexes = tempDict.DateStartIndex.split(",");
												dateValueLengths = tempDict.DateValueLength.split(",");
												chartData = "";
												
												for (y = 0; y < dateValueLengths.length; y++  )
												{
													dateStartIndex = dateStartIndexes[y];
													dateValueLength = dateValueLengths[y];
													
													for (z = 0; z < validMetrics.length; z++ )
													{
														if ((z >= dateStartIndex)&&(cntr<=dateValueLength))
														{
															chartData +=  validMetrics[z] + ",";
															cntr++;
														}
													}
												}
												
												chartData = chartData.substring(0,chartData.lastIndexOf(","));
												//Alert.show(chartData);
											}
											
											tempDict.Metrics = chartData;
											tmpCatArray = tempDict.Metrics.split(",");
											this.alChart.SetCategoryAxisValues(tmpCatArray.reverse().join());
											this.alChart.SetCategory(tempDict.Chart_By);
											//this.alChart.SetCategoryAxisValues(tempDict.Metrics);
											//Alert.show("chartBy:"+tempDict.Chart_By);
										}
										else
										{
											//Alert.show("custom chart data(2)");
											newChartData = "";
											allPossibleMetrics = chartData.split(',');
											
											if (dateIndexes.length == 0 )
											{
												//Alert.show("There Are No Data Sets Available For Selection(2)");
											}
											else
											{
												for (a = dateIndexes[0]; a <= dateIndexes.length+1; a++ )
												{
													//Alert.show("this is allPossibleMetrics:" + allPossibleMetrics[a]+"and a="+a);
													
													if ((allPossibleMetrics[a] != null)&&(dateIndexes[a-dateIndexes[0]]!=null))
													{
														newChartData += allPossibleMetrics[a] + ",";	
														//Alert.show("this is newChartData:"+newChartData);
													}
												}
												
												//Alert.show("this is newChartData:"+newChartData);
											}
											dataFound = true;
											tempDict.Metrics = newChartData;
											tempFreq++;
											tempDict.Frequency = tempFreq;
											tmpCatArray = tempDict.Metrics.split(",");
											//this.alChart.SetCategoryAxisValues(tmpCatArray.reverse().join());
											this.alChart.SetCategory(tempDict.Chart_By);
											this.alChart.SetCategoryAxisValues(tempDict.Metrics);
											//Alert.show("chartBy:"+tempDict.Chart_By);
										}

									}
									else
									{	
										chartData = d.getValue(tempAliasKey);
										//Alert.show("At alias Key:"+tempAliasKey + "& index:"+i);
										//Alert.show("No Mon Alias Metric Data:"+chartData);
										if ((chartData != null)&&(dateDataFound))
										{
											//this.alChart.SetCategoryAxisValues(chartData);
										
											if (!this.chartHasCustomDatesSubChart)
											{
												dataFound = true;
												tempFreq++;
												tempDict.Frequency = tempFreq;
												//Alert.show("regular chart data(2)");
												if (tempDict.DateStartIndex != null)
												{
													
													chartData = "";
													
													dateStartIndexes = tempDict.DateStartIndex.split(",");
													dateValueLengths = tempDict.DateValueLength.split(",");
													validMetrics = d.getValue(tempAliasKey).split(",");
													//Alert.show("key:"+tempAliasKey+"indexes:"+dateStartIndexes.toString()+":length"+dateValueLengths.toString());
													for (y = 0; y < dateValueLengths.length; y++  )
													{
														//Alert.show("indx i="+i+"-indx y="+y);
														dateStartIndex = dateStartIndexes[y];
														dateValueLength = dateValueLengths[y];
														cntr = new Number(0);
														//Alert.show("indx:"+dateStartIndex+"-"+"len:"+dateValueLength);
														for (z = 0; z < validMetrics.length; z++ )
														{
															
															if ((z >= dateStartIndex)&&(cntr<=dateValueLength))
															{
																chartData +=  validMetrics[z] + ",";
																cntr++;
																//Alert.show("z="+z+"added->"+validMetrics[z]);
															}
														}
													}
													
													chartData = chartData.substring(0,chartData.lastIndexOf(","));
													//Alert.show(validMetrics.toString());
													//Alert.show("key:"+tempAliasKey+"-data="+chartData+"-N-Len="+chartData.length);
												}
											
												//Alert.show(chartData);
												tempDict.Metrics = chartData;
												tmpCatArray = tempDict.Metrics.split(",");
												this.alChart.SetCategoryAxisValues(tmpCatArray.reverse().join());
												this.alChart.SetCategory(tempDict.Chart_By);
												//this.alChart.SetCategoryAxisValues(tempDict.Metrics);
												//Alert.show("chartBy:"+tempDict.Chart_By);
											}
											else
											{
												//Alert.show("custom chart data(2)");
												newChartData = "";
												allPossibleMetrics = chartData.split(',');
												
												if (dateIndexes.length == 0 )
												{
													//Alert.show("There Are No Data Sets Available For Selection(2)");
												}
												else
												{
													for (a = dateIndexes[0]; a <= dateIndexes[dateIndexes.length-1]; a++ )
													{
														//Alert.show("this is allPossibleMetrics:" + allPossibleMetrics[a]+"and a="+a);
														
														if ((allPossibleMetrics[a] != null))
														{
															newChartData += allPossibleMetrics[a] + ",";	
															//Alert.show("this is newChartData:"+newChartData);
														}
													}
												}
												//Alert.show("this is newChartData:"+newChartData);
												dataFound = true;
												tempDict.Metrics = newChartData;
												tempFreq++;
												tempDict.Frequency = tempFreq;
												tmpCatArray = tempDict.Metrics.split(",");
												//this.alChart.SetCategoryAxisValues(tmpCatArray.reverse().join());
												this.alChart.SetCategory(tempDict.Chart_By);
												//this.alChart.SetCategoryAxisValues(tempDict.Metrics.);
												//Alert.show("chartBy*:"+tmpCatArray.reverse().join());
												//Alert.show(tempDict.DateStartIndex);
											}

										}
										else
										{
											//this.dbgText.text = "\nNOdata";
											if ((key.indexOf("Last_") >= 0))
											{
												if (all_d.getValue(key + "_Metric_Values")!=null)
												{
													allTmpKeys = all_d.getValue(key + "_Metric_Values");
													
													//Alert.show(allTmpKeys);
													//if (allTmpKeys.indexOf("|") >= 0)
													if((allTmpKeys.indexOf(allCatName)<0))
													{
														allTmpKeys = allCatName + "," + allTmpKeys;
													}
													
												}
												else
												{
													//this.dbgText.text += "\nwz!thr=" + allTmpKeys;
													if (allTmpKeys != null)
													{
														allTmpKeys += allCatName;
													}
													else
													{
														allTmpKeys = allCatName;
													}
												}
												
												
												all_d.setValue(key + "_Metric_Values", allTmpKeys);
												//this.dbgText.text +="\n+key wz =>"+allTmpKeys+":"+key + "_Metric_Values";
											}
											
											
											if(tempDict["Chart_By_Alias"]==allCatAliasName)
											{
												if ((aliasKey.indexOf("Last_") >= 0))
												{
													if (all_d.getValue(aliasKey + "_Metric_Values")!=null)
													{
														allTmpKeys = all_d.getValue(aliasKey + "_Metric_Values");
														
														//Alert.show(allTmpKeys);
														//if (allTmpKeys.indexOf("|") >= 0)
														if((allTmpKeys.indexOf(allCatAliasName)<0))
														{
															allTmpKeys = allCatAliasName + "," + allTmpKeys;
														}
														
													}
													else
													{
														//this.dbgText.text += "\nwz!thr=" + allTmpKeys;
														if (allTmpKeys != null)
														{
															allTmpKeys += allCatAliasName;
														}
														else
														{
															allTmpKeys = allCatAliasName;
														}
													}
													
													
													all_d.setValue(aliasKey + "_Metric_Values", allTmpKeys);
													//this.dbgText.text +="\n+key wz =>"+allTmpKeys+":"+key + "_Metric_Values";
												}
											}
										}
										
										// save data back to dict
										calcData[i] = tempDict;
									}
								}
								
								if (alChart.GetCategoryAxisValues()[i] == null)
								{
									//Alert.show("no dates for this category:"+i);
									alChart.SetCategoryAxisDates("");
									alChart.SetCategoryAxisValues("");
									alChart.SetCategory("");
								}
								else
								{
									//Alert.show("got dates:"+alChart.GetCategoryAxisDates());
								}
								
							}
							
							//Alert.show("allTmpKeys:"+allTmpKeys);
							if (allTmpKeys != null)
							{
								if (h > 0)
								{
									allTmpKeys = "|" + allTmpKeys;
									//this.dbgText.text += "\n+key wz =>" + allTmpKeys + ":" + key + "_Metric_Values...";
									all_d.setValue(key + "_Metric_Values", allTmpKeys);
								}
							}
							
							//Alert.show("allTmpDateKeys:"+allTmpDateKeys);
							if (allTmpDateKeys != null)
							{
								if (h > 0)
								{
									allTmpDateKeys = "|" + allTmpDateKeys;
									all_d.setValue(key + "_Date_Values", allTmpDateKeys);
								}
							}
						}
					}
					
					// get the metric by aggregate and label
					if (!lastDateChartBySet)
					{
						//Alert.show("test");
						//this.chartEndDate.visible = true;
						//this.chartStartDate.visible = true;
						//this.chartStartLabel.visible = true;
						//this.chartEndLabel.visible = true;
						//this.chartCustomSubChart.visible = true;
						
						
						if (allCnt <= 1)
						{
							// save metric values and label
							this.alChart.SetAggregationData(calcData);
							//this.alChart.SetKPIName(this.filters[1]);
							//Alert.show(this.chartFilters[1]);
							//this.dbgText.text = "set Aggregate for only one";
							//Alert.show("this is for only one aggregate");
						}
						else
						{
							//Alert.show("aggregate for more than one");
							// pick the leftmost one to chart by
							// create a map/dict for the fields
							//this.dbgText.text += "\nallCats:" + allCategories;
							//allChartByCategories = allCategories.split(",");
							
							// get All category
							//Alert.show("cbl:"+chartByLabels[h+2].text);
							allString= this.chartByLabels[h+2].text;// 
							var nextAllCategory:String = allString.substring(allString.indexOf("All_")+4, allString.length -1);
							//var nextAllCategory:String = allString.substring(allString.indexOf("All_") + 4, allString.length -1);
							
							//this.dbgText.text += "\n-this is the cat:" + allCategory;
							//Alert.show("allstring:"+allString);
							
							// get field for index
							dataKeyIndex = appLoader.GetChartDataKeys().indexOf(nextAllCategory + "_Values");
							allCategories = appLoader.GetChartDataKeyValues()[dataKeyIndex];
							
							//Alert.show(dataKeyIndex+":"+allCategories);
							//this.dbgText.text += "lookup key rslt:\n" + allCategories;
							var nextAllCategories:Array = allCategories.split(",");
							
							for (i = 0; i < allChartByCategories.length; i++ )
							{
									
								//if ((allChartByCategories[i].indexOf("All") < 0)&&(allChartByCategories[i].indexOf("Some") < 0))
								{
									chartData = "";
									
									tempDict = calcData[i];
									
									//Alert.show("i:" + i + " calcdata fo i:" + calcData[i] );
									if (tempDict != null)
									{
									
									
										tempFreq = tempDict.Frequency;
										
										//allCatName = tempDict["Chart_By"];
										allCatAliasName = tempDict["Chart_By_Alias"];
										
										//Alert.show("acan:"+allCatAliasName+":i="+i);
										if (allCatAliasName=="")// works for 1st All
										{
											allCatAliasName = allChartByCategories[i];
											//Alert.show("yes["+h+"]:"+allCatAliasName);
										}
										else 
										{
											//Alert.show("no:"+allCatAliasName);
										}
									}
									
									
									// regular metric
									tempKey = key.substring(0, key.indexOf("All_")) + allChartByCategories[i] + key.substring(key.indexOf("_"+allCategory) + allCategory.length+2) + "_Metric_Values";
									// regular date
									tempDateKey = key.substring(0, key.indexOf("All_")) + allChartByCategories[i] + key.substring(key.indexOf("_"+allCategory) + allCategory.length+2) + "_Date_Values";
									// no kpi date 
									cbs = this.filterByComboBoxes[1];
									tempNoKpiDateKey = key.substring(0, key.indexOf("All_")) + allChartByCategories[i] + key.substring(key.indexOf("_"+allCategory) + allCategory.length+2);// + "_Date_Values";
									tempNoKpiDateKey = tempNoKpiDateKey.substring(0, tempNoKpiDateKey.indexOf(cbs.selectedLabel)) +  "Date_Values";
									// no month metric
									tempNoMonthKey = rawDatesKey.substring(0, rawDatesKey.indexOf("All_")) + allChartByCategories[i] + rawDatesKey.substring(key.indexOf("_"+allCategory) + allCategory.length+2) + "_Metric_Values";
								
									// regular alias metric
									tempAliasKey = aliasKey.substring(0, aliasKey.indexOf("All_")) + allCatAliasName + aliasKey.substring(aliasKey.indexOf("_"+allCategory) + allCategory.length+2) + "_Metric_Values";
									// regular alias date
									tempDateAliasKey = aliasKey.substring(0,aliasKey.indexOf("All_")) + allCatAliasName + aliasKey.substring(aliasKey.indexOf("_"+allCategory) + allCategory.length+2) + "_Date_Values";
									// no kpi alias date 
									tempNoKpiAliasDateKey = aliasKey.substring(0, aliasKey.indexOf("All_")) + allCatAliasName + aliasKey.substring(aliasKey.indexOf("_"+allCategory) + allCategory.length+2);// + "_Date_Values";
									tempNoKpiAliasDateKey = tempNoKpiAliasDateKey.substring(0, tempNoKpiAliasDateKey.indexOf(cbs.selectedLabel)) +  "Date_Values";
									// no month alias metric
									tempNoMonthAliasKey = rawDatesAliasKey.substring(0, rawDatesAliasKey.indexOf("All_")) + allCatAliasName + rawDatesAliasKey.substring(rawDatesAliasKey.indexOf("_"+allCategory) + allCategory.length+2) + "_Metric_Values";
								
									if (someChartBySet && tempKey.indexOf("All")>=0)
									{
										tempKey = tempKey.substring(tempKey.indexOf("Some_Prdct_Nms")+15);
										tempDateKey = tempDateKey.substring(tempDateKey.indexOf("Some_Prdct_Nms")+15);
										tempNoKpiDateKey = tempNoKpiDateKey.substring(tempNoKpiDateKey.indexOf("Some_Prdct_Nms")+15);
										tempNoMonthKey = tempNoMonthKey.substring(tempNoMonthKey.indexOf("Some_Prdct_Nms")+15);
										tempAliasKey =  tempAliasKey.substring(tempAliasKey.indexOf("Some_Prdct_Nms") + 15);
										tempDateAliasKey =  tempDateAliasKey.substring(tempDateAliasKey.indexOf("Some_Prdct_Nms")+15);
										tempNoKpiAliasDateKey =  tempNoKpiAliasDateKey.substring(tempNoKpiAliasDateKey.indexOf("Some_Prdct_Nms")+15);
										tempNoMonthAliasKey =  tempNoMonthAliasKey.substring(tempNoMonthAliasKey.indexOf("Some_Prdct_Nms")+15);
									}
									//Alert.show("+"+tempKey+"["+i+"]");
									//Alert.show("++" + tempAliasKey);
									//Alert.show("++"+tempAliasKey+"["+tempDict["Chart_By_Alias"]+"]");
									//Alert.show("DateKey:"+tempDateKey);
									//this.dbgText.text += "\ntemp key is:"+tempKey;
									//Alert.show("nextAll:"+nextAllCategories.join());
									//Alert.show("cat:"+nextAllCategories);
									
									for (j = 0; j < nextAllCategories.length; j++ )
									{
										if (nextAllCategories[j].indexOf("All") < 0)
										{
											var nextTempKey:String = tempKey.substring(0, tempKey.indexOf("All_")) + nextAllCategories[j] + tempKey.substring(tempKey.indexOf("All_") + allCategory.length+4);//4 for the all ;
											var nextTempDateKey:String = tempDateKey.substring(0, tempDateKey.indexOf("All_")) + nextAllCategories[j] + tempDateKey.substring(tempDateKey.indexOf("All_") + allCategory.length+4);//4 for the all ;
											var nextTempNoKpiDateKey:String = tempNoKpiDateKey.substring(0, tempNoKpiDateKey.indexOf("All_")) + nextAllCategories[j] + tempNoKpiDateKey.substring(tempNoKpiDateKey.indexOf("All_") + allCategory.length+4);//4 for the all ;
											var nextTempNoMonthKey:String = tempNoMonthKey.substring(0, tempNoMonthKey.indexOf("All_")) + nextAllCategories[j] + tempNoMonthKey.substring(tempNoMonthKey.indexOf("All_") + allCategory.length+4);//4 for the all ;
									
											var nextTempDateKeyIndex:String = nextTempNoKpiDateKey + "_Index";
											var nextTempDateKeyLength:String = nextTempNoKpiDateKey + "_Length";
											
											var nextTempAliasKey:String = tempAliasKey.substring(0, tempAliasKey.indexOf("All_")) + nextAllCategories[j] + tempAliasKey.substring(tempAliasKey.indexOf("All_") + allCategory.length+4);//4 for the all ;
											var nextTempDateAliasKey:String = tempDateAliasKey.substring(0, tempDateAliasKey.indexOf("All_")) + nextAllCategories[j] + tempDateAliasKey.substring(tempDateAliasKey.indexOf("All_") + allCategory.length+4);//4 for the all ;
											var nextTempNoKpiDateAliasKey:String = tempNoKpiAliasDateKey.substring(0, tempNoKpiAliasDateKey.indexOf("All_")) + nextAllCategories[j] + tempNoKpiAliasDateKey.substring(tempNoKpiAliasDateKey.indexOf("All_") + allCategory.length+4);//4 for the all ;
											var nextTempNoMonthAliasKey:String = tempNoMonthAliasKey.substring(0, tempNoMonthAliasKey.indexOf("All_")) + nextAllCategories[j] + tempNoMonthAliasKey.substring(tempNoMonthAliasKey.indexOf("All_") + allCategory.length+4);//4 for the all ;
											
											var nextTempDateAliasKeyIndex:String = nextTempNoKpiDateAliasKey + "_Index";
											var nextTempDateAliasKeyLength:String = nextTempNoKpiDateAliasKey + "_Length";
											
											dateDataFound = false;
											//Alert.show("currentKey:"+nextTempNoKpiDateAliasKey+"-Opr1_Night_Shift_January_ColdDesk_Date_Values");
											// do date data
											chartData = d.getValue(nextTempDateKey);
											//chartData = d.getValue("Opr1_Night_Shift_January_ColdDesk_Date_Values");
											
											if (chartData != null)
											{
												//Alert.show("not NUll:"+chartData);
												// set the chart string data
												if (this.chartHasCustomDatesSubChart)
												{
													//Alert.show("custom dates with data");
													
													startDate = new Date();
													startDate.setTime(Date.parse(this.chartStartDate.label));
													
													endDate = new Date();
													endDate.setTime(Date.parse(this.chartEndDate.label));
													
													actualDate = new Date();
													allPossibleDates = chartData.split(',');
													newChartData = "";
													dateIndexes = new Array();
													
													for (a = 0; a < allPossibleDates.length; a++ )
													{
														date = allPossibleDates[a] as String;
														if (date != null)
														{
															//Alert.show(date+":"+startDate.toString()+":"+endDate.toString());
															try
															{
																actualDate.setTime(Date.parse(date));
																
																// is it in the range?
																if ((actualDate.getTime() >= startDate.getTime()) && (actualDate.getTime() <= endDate.getTime()))
																{
																	newChartData += allPossibleDates[a] + ",";
																	dateIndexes.push(a);
																	//Alert.show("this is a:"+a);
																}
															}
															catch (err:Error)
															{
																Alert.show("[ERROR] problems loading custom dates:"+err.message);
															}
														}
													}
													
													//dataFound = true;
													dateDataFound = true;
													tempDict.Dates = newChartData;
													tempDict.DateIndexes = dateIndexes;
													//Alert.show("DateIndexes going in:"+tempDict.DateIndexes);
													
												}
												else
												{
													dateDataFound = true;
													
												}
											}
											else
											{
												chartData = d.getValue(nextTempNoKpiDateKey);
											
												if (chartData != null)
												{
													// set the chart string data
													if (this.chartHasCustomDatesSubChart)
													{
														//Alert.show("custom dates with data");
														
														startDate = new Date();
														startDate.setTime(Date.parse(this.chartStartDate.label));
														
														endDate = new Date();
														endDate.setTime(Date.parse(this.chartEndDate.label));
														
														actualDate = new Date();
														allPossibleDates = chartData.split(',');
														newChartData = "";
														dateIndexes = new Array();
														
														for (a = 0; a < allPossibleDates.length; a++ )
														{
															date = allPossibleDates[a] as String;
															if (date != null)
															{
																//Alert.show(date+":"+startDate.toString()+":"+endDate.toString());
																try
																{
																	actualDate.setTime(Date.parse(date));
																	
																	// is it in the range?
																	if ((actualDate.getTime() >= startDate.getTime()) && (actualDate.getTime() <= endDate.getTime()))
																	{
																		newChartData += allPossibleDates[a] + ",";
																		dateIndexes.push(a);
																		//Alert.show("this is a:"+a);
																	}
																}
																catch (err:Error)
																{
																	Alert.show("[ERROR] problems loading custom dates:"+err.message);
																}
															}
														}
														
														//dataFound = true;
														dateDataFound = true;
														tempDict.Dates = newChartData;
														tempDict.DateIndexes = dateIndexes;
														//Alert.show("DateIndexes going in:"+tempDict.DateIndexes);
														
													}
													else
													{
														//Alert.show("found something");
														dateDataFound = true;
														tempDict.DateStartIndex = d.getValue(nextTempDateKeyIndex);
														tempDict.DateValueLength = d.getValue(nextTempDateKeyLength);
													}
												}
											}
											
											if (!dateDataFound)
											{
												// do alias date dataok
												chartData = d.getValue(nextTempDateAliasKey);
												
												if (chartData != null)
												{
													//Alert.show("not null");
													// set the chart string data
													if (this.chartHasCustomDatesSubChart)
													{
														//Alert.show("custom dates with data");
														
														startDate = new Date();
														startDate.setTime(Date.parse(this.chartStartDate.label));
														
														endDate = new Date();
														endDate.setTime(Date.parse(this.chartEndDate.label));
														
														actualDate = new Date();
														allPossibleDates = chartData.split(',');
														newChartData = "";
														dateIndexes = new Array();
														
														for (a = 0; a < allPossibleDates.length; a++ )
														{
															date = allPossibleDates[a] as String;
															if (date != null)
															{
																//Alert.show(date+":"+startDate.toString()+":"+endDate.toString());
																try
																{
																	actualDate.setTime(Date.parse(date));
																	
																	// is it in the range?
																	if ((actualDate.getTime() >= startDate.getTime()) && (actualDate.getTime() <= endDate.getTime()))
																	{
																		newChartData += allPossibleDates[a] + ",";
																		dateIndexes.push(a);
																		//Alert.show("this is a:"+a);
																	}
																}
																catch (err:Error)
																{
																	Alert.show("[ERROR] problems loading custom dates:"+err.message);
																}
															}
														}
														
														//dataFound = true;
														dateDataFound = true;
														tempDict.Dates = newChartData;
														tempDict.DateIndexes = dateIndexes;
														//Alert.show("DateIndexes going in:"+tempDict.DateIndexes);
														
													}
													else
													{
														dateDataFound = true;
														tempDict.DateStartIndex = d.getValue(nextTempDateKeyIndex);
														tempDict.DateValueLength = d.getValue(nextTempDateKeyLength);
														//Alert.show(nextTempDateKeyIndex+":"+d.getValue(nextTempDateKeyIndex));
													}
												}
												else
												{
													//Alert.show("last stop fr:"+nextTempNoKpiDateAliasKey);
													chartData = d.getValue(nextTempNoKpiDateAliasKey);
													//chartData = d.getValue("");
													if (chartData != null)
													{
														//Alert.show("luv:"+nextTempNoKpiDateAliasKey);
														// set the chart string data
														if (this.chartHasCustomDatesSubChart)
														{
															//Alert.show("custom dates with data");
															
															startDate = new Date();
															startDate.setTime(Date.parse(this.chartStartDate.label));
															
															endDate = new Date();
															endDate.setTime(Date.parse(this.chartEndDate.label));
															
															actualDate = new Date();
															allPossibleDates = chartData.split(',');
															newChartData = "";
															dateIndexes = new Array();
															
															for (a = 0; a < allPossibleDates.length; a++ )
															{
																date = allPossibleDates[a] as String;
																if (date != null)
																{
																	//Alert.show(date+":"+startDate.toString()+":"+endDate.toString());
																	try
																	{
																		actualDate.setTime(Date.parse(date));
																		
																		// is it in the range?
																		if ((actualDate.getTime() >= startDate.getTime()) && (actualDate.getTime() <= endDate.getTime()))
																		{
																			newChartData += allPossibleDates[a] + ",";
																			dateIndexes.push(a);
																			//Alert.show("this is a:"+a);
																		}
																	}
																	catch (err:Error)
																	{
																		Alert.show("[ERROR] problems loading custom dates:"+err.message);
																	}
																}
															}
															
															//dataFound = true;
															/*
															if (tempDict == null)
															{
																tempDict = new Dictionary(); 
															}
															*/
															
															dateDataFound = true;
															if (tempDict != null )
															{
																tempDict.Dates = newChartData;
																tempDict.DateIndexes = dateIndexes;
																//Alert.show("DateIndexes going in:"+tempDict.DateIndexes);
															}
														}
														else
														{
															//Alert.show("date index data!:"+nextTempDateAliasKeyIndex+":"+d.getValue(nextTempDateAliasKeyIndex));
															dateDataFound = true;
															if (tempDict != null )
															{
																tempDict.DateStartIndex = d.getValue(nextTempDateAliasKeyIndex);
																tempDict.DateValueLength = d.getValue(nextTempDateAliasKeyLength);
																//Alert.show(nextTempDateAliasKeyIndex+":"+d.getValue(nextTempDateAliasKeyIndex));
															}
														}
													}
												}
											}
											
											// do metric data
											chartData = d.getValue(nextTempKey);
											
											if ((chartData != null)&&(dateDataFound))
											{
												if (!this.chartHasCustomDatesSubChart)
												{
													
													tempDict.Metrics += chartData + ",";
													tempFreq++;
													tempDict.Frequency = tempFreq; 
													calcData[i] = tempDict;
													dataFound = true;
												}
												else
												{
													newChartData = "";
													allPossibleMetrics = chartData.split(',');
													dateIndexes = tempDict.DateIndexes;
													
													if ((dateIndexes.length == 0 )&&(key.indexOf("All_")<0))
													{
														Alert.show("There Are No Data Sets Available For Selection(3)");
													}
													else
													{
														for (a = dateIndexes[0]; a <= dateIndexes.length+1; a++ )
														{
															//Alert.show("this is allPossibleMetrics:" + allPossibleMetrics[a]+"and a="+a);
															
															if ((allPossibleMetrics[a] != null)&&(dateIndexes[a-dateIndexes[0]]!=null))
															{
																newChartData += allPossibleMetrics[a] + ",";	
																//Alert.show("this is newChartData:"+newChartData);
															}
														}
													}
													
													tempDict.Metrics += newChartData + ",";
													tempFreq++;
													tempDict.Frequency = tempFreq;
													calcData[i] = tempDict;
													dataFound = true;
													
												}
											}
											else
											{
												// do metric data
												chartData = d.getValue(nextTempNoMonthKey);
												
												if ((chartData != null)&&(dateDataFound))
												{
													if (!this.chartHasCustomDatesSubChart)
													{
														if (tempDict.DateStartIndex != null)
														{
															if ((tempDict.DateStartIndex.length >0))
															{
																
																//Alert.show(tempDict.DateValueLength.toString());
																validMetrics = d.getValue(allTmpKey).split(",");
																chartData = "";
																cntr = new Number(0);
																dateStartIndexes = tempDict.DateStartIndex.split(",");
																dateValueLengths = tempDict.DateValueLength.split(",");
																monthMetrics = new Array();
																for (y = 0; y < dateValueLengths.length; y++  )
																{
																	dateStartIndex = dateStartIndexes[y];
																	dateValueLength = dateValueLengths[y];
																	//Alert.show("startIndx_:" + dateStartIndex + "&length:" + dateValueLength);
																	cntr = 0;
																	for (z = 0; z < validMetrics.length; z++ )
																	{
																		if ((z >= dateStartIndex)&&(cntr<=dateValueLength))
																		{
																			//chartData +=  validMetrics[z] + ",";
																			monthMetrics.push(validMetrics[z]);
																			//Alert.show(validMetrics[z]);
																			cntr++;
																		}
																	}
																}

																//Alert.show(allTmpKey+":"+monthMetrics.toString()+":"+dateValueLength);
																//monthMetrics.reverse();
																//Alert.show("Metrics so far:"+allTempDict.Metrics);
																allTempDict.Metrics += "," +monthMetrics.toString();
																allTempDict.Metrics = allTempDict.Metrics.replace(",,", ",");
																
															}
														}
														else
														{
															//Alert.show("null!!!");
															tempDict.Metrics += chartData + ",";
														}
													
														tempFreq++;
														tempDict.Frequency = tempFreq; 
														calcData[i] = tempDict;
														dataFound = true;
													}
													else
													{
														newChartData = "";
														allPossibleMetrics = chartData.split(',');
														dateIndexes = tempDict.DateIndexes;
														
														if ((dateIndexes.length == 0 )&&(key.indexOf("All_")<0))
														{
															Alert.show("There Are No Data Sets Available For Selection(3)");
														}
														else
														{
															for (a = dateIndexes[0]; a <= dateIndexes.length+1; a++ )
															{
																//Alert.show("this is allPossibleMetrics:" + allPossibleMetrics[a]+"and a="+a);
																
																if ((allPossibleMetrics[a] != null)&&(dateIndexes[a-dateIndexes[0]]!=null))
																{
																	newChartData += allPossibleMetrics[a] + ",";	
																	//Alert.show("this is newChartData:"+newChartData);
																}
															}
														}
														
														tempDict.Metrics += newChartData + ",";
														tempFreq++;
														tempDict.Frequency = tempFreq;
														calcData[i] = tempDict;
														dataFound = true;
														
													}
												}
											}
											
											//if (!dataFound)
											{
												// do metric alias data
												chartData = d.getValue(nextTempAliasKey);
												
												if ((chartData != null)&&(dateDataFound))
												{
													
													if (!this.chartHasCustomDatesSubChart)
													{
														tempDict.Metrics += chartData + ",";
														tempFreq++;
														tempDict.Frequency = tempFreq; 
														calcData[i] = tempDict;
														dataFound = true;
													}
													else
													{
														newChartData = "";
														allPossibleMetrics = chartData.split(',');
														dateIndexes = tempDict.DateIndexes;
														
														if ((dateIndexes.length == 0 )&&(key.indexOf("All_")<0))
														{
															Alert.show("There Are No Data Sets Available For Selection(3)");
														}
														else
														{
															for (a = dateIndexes[0]; a <= dateIndexes.length+1; a++ )
															{
																//Alert.show("this is allPossibleMetrics:" + allPossibleMetrics[a]+"and a="+a);
																
																if ((allPossibleMetrics[a] != null)&&(dateIndexes[a-dateIndexes[0]]!=null))
																{
																	newChartData += allPossibleMetrics[a] + ",";	
																	//Alert.show("this is newChartData:"+newChartData);
																}
															}
														}
														
														tempDict.Metrics += newChartData + ",";
														tempFreq++;
														tempDict.Frequency = tempFreq;
														calcData[i] = tempDict;
														dataFound = true;
														
													}
												}
												else
												{
													// do metric data
													chartData = d.getValue(nextTempNoMonthAliasKey);
													
													//Alert.show("here at no month alias:"+nextTempNoMonthAliasKey+":dateFound?"+dateDataFound+":"+chartData+":"+tempDict.DateStartIndex);
													
													if ((chartData != null)&&(dateDataFound))
													{
														if (!this.chartHasCustomDatesSubChart)
														{
															if (tempDict != null)
															{
																if (tempDict.DateStartIndex != null)
																{
																	if ((tempDict.DateStartIndex.length >0))
																	{
																		
																		//Alert.show(tempDict.DateStartIndex);
																		validMetrics = d.getValue(nextTempNoMonthAliasKey).split(",");
																		//chartData = "";
																		cntr = new Number(0);
																		dateStartIndexes = tempDict.DateStartIndex.split(",");
																		dateValueLengths = tempDict.DateValueLength.split(",");
																		monthMetrics = new Array();
																		for (y = 0; y < dateValueLengths.length; y++  )
																		{
																			dateStartIndex = dateStartIndexes[y];
																			dateValueLength = dateValueLengths[y];
																			//Alert.show("startIndx_:" + dateStartIndex + "&length:" + dateValueLength);
																			cntr = 0;
																			for (z = 0; z < validMetrics.length; z++ )
																			{
																				if ((z >= dateStartIndex)&&(cntr<=dateValueLength))
																				{
																					//chartData +=  validMetrics[z] + ",";
																					monthMetrics.push(validMetrics[z]);
																					//Alert.show(validMetrics[z]);
																					cntr++;
																				}
																			}
																		}

																		//Alert.show(nextTempNoMonthAliasKey+":"+monthMetrics.toString()+":"+dateValueLength);
																		//Alert.show("Metrics so far:"+tempDict.Metrics);
																		tempDict.Metrics += "," +monthMetrics.toString();
																		tempDict.Metrics = tempDict.Metrics.replace(",,", ",");
																		
																	}
																}
																else
																{
																	//Alert.show("null!!!");
																	tempDict.Metrics += chartData + ",";
																}
																
																//Alert.show("All-All-Metrics:"+tempDict.Metrics);
																//tempDict.Metrics += chartData + ",";
																tempFreq++;
																tempDict.Frequency = tempFreq; 
																calcData[i] = tempDict;
																dataFound = true;
															}
														}
														else
														{
															newChartData = "";
															allPossibleMetrics = chartData.split(',');
															
															if (tempDict != null)
															{
																dateIndexes = tempDict.DateIndexes;
																
																if ((dateIndexes.length == 0 )&&(key.indexOf("All_")<0))
																{
																	Alert.show("There Are No Data Sets Available For Selection(3)");
																}
																else
																{
																	for (a = dateIndexes[0]; a <= dateIndexes.length+1; a++ )
																	{
																		//Alert.show("this is allPossibleMetrics:" + allPossibleMetrics[a]+"and a="+a);
																		
																		if ((allPossibleMetrics[a] != null)&&(dateIndexes[a-dateIndexes[0]]!=null))
																		{
																			newChartData += allPossibleMetrics[a] + ",";	
																			//Alert.show("this is newChartData:"+newChartData);
																		}
																	}
																}
																
																tempDict.Metrics += newChartData + ",";
																tempFreq++;
																tempDict.Frequency = tempFreq;
																calcData[i] = tempDict;
																dataFound = true;
															}
														}
													}
												}
											}
										}
									}
								}
							}
							//this.dbgText.text = "set Aggregate for multiple";
							
							// save metric values and label
							this.alChart.SetAggregationData(calcData);
							//Alert.show(this.chartFilters[1]);
						}
						
						if (dataFound)
						{
							// set the chart string data
							this.chartMessages.text += "\n:Found data available for \n"+ key+"_Values";
						}
						else
						{
							this.chartMessages.text += "\n:There is no data available for \n" + key + "_Values";
							if (key.indexOf("Last_") < 0)
							{
								//Alert.show("There Are No Data Sets Available for Selection(-2)");
							}
						}
					}
					else
					{
						dataFound = false;
						allChartByData = calcData;
						//Alert.show("lastChartbay not set")
					}
					
					//handlingAlls = true;
				}
				
				//this.dbgText.text += "before lastDateHandler";
				//Alert.show("chartBy Data Length:"+allChartByData.length);
				//Alert.show("b4:" + chartCustomSubChart);
				// handle last dates
				if (lastDateChartBySet||chartHasCustomDatesSubChart)
				{
					//Alert.show("after:");
					if ((this.chartByLabels[0].text.indexOf("Some_") >= 0)||(this.chartByLabels[0].text.indexOf("All_") >= 0))
					{
						
					
					}
					else
					{
						this.chartEndDate.visible = true;
						this.chartStartDate.visible = true;
						this.chartStartLabel.visible = true;
						this.chartEndLabel.visible = true;
						//this.chartCustomSubChart.visible = true;
						this.chartEndChooser.visible = true;
						this.chartStartChooser.visible = true;
						this.customDateRangeLabel.visible = true;
					}	
					
					var monthDate:Date;
					var monthName:String;
					var allTempDict:Dictionary;
					var tempDict:Dictionary;
					var tempKey:String;
					var tempDateKey:String;
					var tempDateKeyIndex:String;
					var tempDateKeyIndexLength:String;
					var tempDateAliasKeyIndex:String;
					var tempDateAliasKeyIndexLength:String;
					var tempAliasKey:String;
					var tempDateAliasKey:String;
					var tempNoKpiDateKey:String;
					var tempNoKpiAliasDateKey:String;
					var tempNoMonthKey:String;
					var tempNoMonthAliasKey:String;
					var tempFreq:Number = new Number(0);
					var allTempFreq:Number;
					var ctr:Number = 0;
					var allTmpKey:String;
					var allTmpDateKey:String;
					var allTmpCats:String;
					var allTmpDateCats:String;
					var k:Number = 0;
							
					//this.dbgText.text += "before key IndexOf";
					if (!chartHasCustomDatesSubChart)
					{
						if (key.indexOf("Last_12_Mns") >= 0)
						{
							// calculate boundary date
							boundaryDate = new Date(currentDate.fullYear - 1, currentDate.month+1, currentDate.date);
							
							rawDatesKey = key.substring(0, key.indexOf("_Last_12_Mns")) + key.substring(key.indexOf("_Last_12_Mns") + 12 );
							rawDatesAliasKey = aliasKey.substring(0, aliasKey.indexOf("_Last_12_Mns")) + aliasKey.substring(aliasKey.indexOf("_Last_12_Mns") + 12); 
						
						}
						
						if (key.indexOf("Last_9_Mns") >= 0)
						{
							// calculate boundary date
							boundaryDate = new Date(currentDate.fullYear,currentDate.month-9,currentDate.date);
						
							rawDatesKey = key.substring(0, key.indexOf("_Last_9_Mns")) + key.substring(key.indexOf("_Last_9_Mns") + 11 );
							rawDatesAliasKey = aliasKey.substring(0, aliasKey.indexOf("_Last_9_Mns")) + aliasKey.substring(aliasKey.indexOf("_Last_9_Mns") + 11); 
						}
						
						if (key.indexOf("Last_6_Mns") >= 0)
						{
							// calculate boundary date
							boundaryDate = new Date(currentDate.fullYear,currentDate.month-6,currentDate.date);
						
							rawDatesKey = key.substring(0, key.indexOf("_Last_6_Mns")) + key.substring(key.indexOf("_Last_6_Mns") + 11 );
							rawDatesAliasKey = aliasKey.substring(0, aliasKey.indexOf("_Last_6_Mns")) + aliasKey.substring(aliasKey.indexOf("_Last_6_Mns") + 11); 
						}
						
						if (key.indexOf("Last_3_Mns") >= 0)
						{
							// calculate boundary date
							boundaryDate = new Date(currentDate.fullYear,currentDate.month-3,currentDate.date);
						
							rawDatesKey = key.substring(0, key.indexOf("_Last_3_Mns")) + key.substring(key.indexOf("_Last_3_Mns") + 11 );
							rawDatesAliasKey = aliasKey.substring(0, aliasKey.indexOf("_Last_3_Mns")) + aliasKey.substring(aliasKey.indexOf("_Last_3_Mns") + 11); 
						}
						
						this.chartStartChooser.selectedDate = new Date(boundaryDate.fullYear,boundaryDate.month,1);
						this.chartStartDate.label = new String(boundaryDate.month+1) + "/01/" +boundaryDate.fullYear.toString();
					
					}
					else 
					{
						// calculate boundary date/start date
						boundaryDate = new Date();
						boundaryDate.setTime(Date.parse(this.chartStartDate.label));
						
						currentDate.setTime(Date.parse(this.chartEndDate.label));
						// calculate currentDate/startDate
						//Alert.show("Custom_Dates:" + boundaryDate.getTime() +":" + currentDate.getTime());
						//Alert.show("Custom_Dates:" + boundaryDate +":" + currentDate.toString());
						//rawDatesKey = key;
						//rawDatesAliasKey = aliasKey; 
						//Alert.show("key:" + key);
						//Alert.show("aliasKey:"+aliasKey);
						//Alert.show("mon:" + this.chartByLabels[2].text)
						if ((key.indexOf("Last_") < 0)&&(boundaryDate.month == currentDate.month))
						{
							key =this.setCustomDateKey(boundaryDate,currentDate,key);
							aliasKey = this.setCustomDateKey(boundaryDate,currentDate, aliasKey);
						}
						
						rawDatesKey = key.substring(0, key.indexOf("_Last")) + key.substring(key.indexOf("_Last") + 6 );
						rawDatesAliasKey = aliasKey.substring(0, aliasKey.indexOf("_Last")) + aliasKey.substring(aliasKey.indexOf("_Last") + 6); 
						//Alert.show("Alias Dates Keys:"+key);
					}
					
					monthDate = boundaryDate;
					calcData = new Array();
					//this.dbgText.text += "before month buckets";
					//Alert.show("monDate:" + monthDate.toDateString() + "-key:" + aliasKey + "-currDate:" + currentDate.toDateString());
					
					// load month values in dict that fall within boundary
					while(monthDate.getTime()<=currentDate.getTime())
					{
						//this.dbgText.text += monthDate.getTime() + "="+currentDate.getTime();
						
						// create a map/dict for the fields
						dict = new Dictionary();
						
						switch(monthDate.getMonth())
						{
							case 0:
								dict["Month"] = new String("January"); // month
								dict["Data"] = new String("") // copy of data
								dict["Dates"] = new String("") // copy of dates
								dict["Metrics"] = new String("") // copy of metrics
								dict["Labels"] = new String("") // what shows on the chart for values
								dict["Frequency"] = new Number(0); // frequency of month
								dict["Calculation"] = new String("Average"); // Average? hard coded for now
								dict["DateIndexes"] = new Array();
								dict["DateStartIndex"] = new String();
								dict["DateValueLength"] = new String();
								break;
							case 1:
								dict["Month"] = new String("February"); // month
								dict["Data"] = new String("") // copy of data
								dict["Dates"] = new String("") // copy of dates
								dict["Metrics"] = new String("") // copy of metrics
								dict["Labels"] = new String("") // what shows on the chart for values
								dict["Frequency"] = new Number(0); // frequency of month
								dict["Calculation"] = new String("Average"); // Average?
								dict["DateIndexes"] = new Array();
								dict["DateStartIndex"] = new String();
								dict["DateValueLength"] = new String();
								break;
							case 2:
								dict["Month"] = new String("March"); // month
								dict["Data"] = new String("") // copy of data
								dict["Dates"] = new String("") // copy of dates
								dict["Metrics"] = new String("") // copy of metrics
								dict["Labels"] = new String("") // what shows on the chart for values
								dict["Frequency"] = new Number(0); // frequency of month
								dict["Calculation"] = new String("Average"); // Average?
								dict["DateIndexes"] = new Array();
								dict["DateStartIndex"] = new String();
								dict["DateValueLength"] = new String();
								break;
							case 3:
								dict["Month"] = new String("April"); // month
								dict["Data"] = new String("") // copy of data
								dict["Dates"] = new String("") // copy of dates
								dict["Metrics"] = new String("") // copy of metrics
								dict["Labels"] = new String("") // what shows on the chart for values
								dict["Frequency"] = new Number(0); // frequency of month
								dict["Calculation"] = new String("Average"); // Average?
								dict["DateIndexes"] = new Array();
								dict["DateStartIndex"] = new String();
								dict["DateValueLength"] = new String();
								break;
							case 4:
								dict["Month"] = new String("May"); // month
								dict["Data"] = new String("") // copy of data
								dict["Dates"] = new String("") // copy of dates
								dict["Metrics"] = new String("") // copy of metrics
								dict["Labels"] = new String("") // what shows on the chart for values
								dict["Frequency"] = new Number(0); // frequency of month
								dict["Calculation"] = new String("Average"); // Average?
								dict["DateIndexes"] = new Array();
								dict["DateStartIndex"] = new String();
								dict["DateValueLength"] = new String();
								break;
							case 5:
								dict["Month"] = new String("June"); // month
								dict["Data"] = new String("") // copy of data
								dict["Dates"] = new String("") // copy of dates
								dict["Metrics"] = new String("") // copy of metrics
								dict["Labels"] = new String("") // what shows on the chart for values
								dict["Frequency"] = new Number(0); // frequency of month
								dict["Calculation"] = new String("Average"); // Average?
								dict["DateIndexes"] = new Array();
								dict["DateStartIndex"] = new String();
								dict["DateValueLength"] = new String();
								break;
							case 6:
								dict["Month"] = new String("July"); // month
								dict["Data"] = new String("") // copy of data
								dict["Dates"] = new String("") // copy of dates
								dict["Metrics"] = new String("") // copy of metrics
								dict["Labels"] = new String("") // what shows on the chart for values
								dict["Frequency"] = new Number(0); // frequency of month
								dict["Calculation"] = new String("Average"); // Average?
								dict["DateIndexes"] = new Array();
								dict["DateStartIndex"] = new String();
								dict["DateValueLength"] = new String();
								break;
							case 7:
								dict["Month"] = new String("August"); // month
								dict["Data"] = new String("") // copy of data
								dict["Dates"] = new String("") // copy of dates
								dict["Metrics"] = new String("") // copy of metrics
								dict["Labels"] = new String("") // what shows on the chart for values
								dict["Frequency"] = new Number(0); // frequency of month
								dict["DateIndexes"] = new Array();
								dict["Calculation"] = new String("Average"); // Average?
								dict["DateStartIndex"] = new String();
								dict["DateValueLength"] = new String();
								break;
							case 8:
								dict["Month"] = new String("September"); // month
								dict["Data"] = new String("") // copy of data
								dict["Dates"] = new String("") // copy of dates
								dict["Metrics"] = new String("") // copy of metrics
								dict["Labels"] = new String("") // what shows on the chart for values
								dict["Frequency"] = new Number(0); // frequency of month
								dict["Calculation"] = new String("Average"); // Average?
								dict["DateIndexes"] = new Array();
								dict["DateStartIndex"] = new String();
								dict["DateValueLength"] = new String();
								break;
							case 9:
								dict["Month"] = new String("October"); // month
								dict["Data"] = new String("") // copy of data
								dict["Dates"] = new String("") // copy of dates
								dict["Metrics"] = new String("") // copy of metrics
								dict["Labels"] = new String("") // what shows on the chart for values
								dict["Frequency"] = new Number(0); // frequency of month
								dict["Calculation"] = new String("Average"); // Average?
								dict["DateIndexes"] = new Array();
								dict["DateStartIndex"] = new String();
								dict["DateValueLength"] = new String();
								break;
							case 10:
								dict["Month"] = new String("November"); // month
								dict["Data"] = new String("") // copy of data
								dict["Dates"] = new String("") // copy of dates
								dict["Metrics"] = new String("") // copy of metrics
								dict["Labels"] = new String("") // what shows on the chart for values
								dict["Frequency"] = new Number(0); // frequency of month
								dict["Calculation"] = new String("Average"); // Average?
								dict["DateIndexes"] = new Array();
								dict["DateStartIndex"] = new String();
								dict["DateValueLength"] = new String();
								break;
							case 11:
								dict["Month"] = new String("December"); // month
								dict["Data"] = new String("") // copy of data
								dict["Dates"] = new String("") // copy of dates
								dict["Metrics"] = new String("") // copy of metrics
								dict["Labels"] = new String("") // what shows on the chart for values
								dict["Frequency"] = new Number(0); // frequency of month
								dict["DateIndexes"] = new Array();
								dict["Calculation"] = new String("Average"); // Average?
								dict["DateStartIndex"] = new String();
								dict["DateValueLength"] = new String();
								break;	
						}
						
						var currentMonthDate:Date = new Date(monthDate.fullYear, monthDate.month + 1, monthDate.date);
						var randomArray:Array;
						//this.dbgText.text += currentMonthDate.toDateString();
						
						if (currentMonthDate.getTime() <= currentDate.getTime())
						{
							// add to dataSet
							calcData.push(dict);
						}
						else
						{
							if (chartHasCustomDatesSubChart)
							{
								// add to dataSet
								calcData.push(dict);
							}
						}
						
						// increment date
						monthDate = currentMonthDate;
					}
					
					//this.dbgText.text += "after month buckets";
					
					if (!this.chartHasCustomDatesSubChart)
					{
						this.chartEndChooser.selectedDate = new Date((new Date()).fullYear, (new Date()).month+1,0);
						this.chartEndDate.label = new String(this.chartEndChooser.selectedDate.month+1) + "/" + new String(this.chartEndChooser.selectedDate.date) + "/" +this.chartEndChooser.selectedDate.fullYear.toString();
					}
					else
					{
						allChartByData = calcData;
					}
					
					//Alert.show("Calc Data len:" + calcData.length);
					//Alert.show("custom date:" + chartHasCustomDatesSubChart)

					// load the data and save frequency data
					for ( i= 0; i < calcData.length;i++ )
					{
						dateDataFound = false;
						dataFound = false;
						chartData = null;
						
						tempDict = calcData[i];
						tempFreq = tempDict.Frequency;
						monthName = tempDict["Month"];
						
						
						
						// if this is a piped one
						{
							tempKey = key.substring(0, key.indexOf("Last_")) + monthName + key.substring(key.indexOf("_Mns")+4)+ "_Values";
							tempAliasKey = aliasKey.substring(0, aliasKey.indexOf("Last_")) + monthName + aliasKey.substring(aliasKey.indexOf("_Mns")+4)+ "_Values";
							
							if (!handlingAlls)
							{
								//Alert.show("here!handling alls for Last");
								// use key to get the data
								chartData = d.getValue(tempKey);
								
								if (chartData == null)
								{
									chartData = d.getValue(tempAliasKey);
								}
								
								//Alert.show("data:"+chartData);
								
								if (chartData == null)
								{
									dateDataFound = false;
									//dataFound = false;
								}
							}
							else
							{
								//Alert.show("here handling alls! for Last");
								chartData = null;
								allTmpKey = key + "_Values";
								allTmpCats = all_d.getValue(allTmpKey);
								
								//this.dbgText.text += "\nafter all tmp key:" + allTmpKey + "|cats:" + allTmpCats+"\n";
								
								if (allTmpCats != null)
								{
									all_calcData = allTmpCats.split(",");
									chartData = "";
									// loop through keys contatonating data
									for (j = 0; j < all_calcData.length;j++ )
									{
										allTmpKey = all_calcData[j] + tempKey.substring(tempKey.indexOf(allString) + allString.length);
										//this.dbgText.text += "after all tmp data key val:" + allTmpKey;
										if (d.getValue(allTmpKey) != null)
										{
											chartData += ","+d.getValue(allTmpKey);
										}
									}
									
									if (chartData == "")
									{
										chartData = null;
									}
									
								}
							}

							//Alert.show("apparently not null here:"+chartData)
							if ((chartData != null))
							{
								dataFound = true;
								dateDataFound = true;
								tempDict.Data = chartData;
								//Alert.show("we have chart Data?");
							}
						}
						//Alert.show("dateFound here is:" + dateDataFound);
						// regular Dates W/ KPI
						if (!dateDataFound)
						{
							//Alert.show("still"+monthName);
							if (key.indexOf("Mns") >= 0)
							{
								// if this is a date metric one
								tempKey = key.substring(0, key.indexOf("Last_")) + monthName + key.substring(key.indexOf("_Mns") + 4) + "_Date_Values";
								tempAliasKey = aliasKey.substring(0, aliasKey.indexOf("Last_")) + monthName + aliasKey.substring(aliasKey.indexOf("_Mns") + 4) + "_Date_Values";
								//Alert.show("Key Val1:"+tempKey);
							}
							else
							{
								tempKey = key.substring(0, key.indexOf(monthName)) + monthName + "_Date_Values";
								tempAliasKey = aliasKey.substring(0, aliasKey.indexOf(monthName)) + monthName + "_Date_Values";
								//Alert.show("Key Val2:"+tempKey);
							}
							//Alert.show("tmpKey:"+tempKey);
							
							// use key to get the data
							if (!handlingAlls)
							{
								// use key to get the data
								//tempDict.Labels = d.getValue("Month_Values");
								chartData = d.getValue(tempKey);
								
								if (chartData == null)
								{
									chartData = d.getValue(tempAliasKey);
								}
								
								if (chartData == null)
								{
									dateDataFound = false;
								}
								else
								{
									dateDataFound = true;
								}
							}
							else
							{
								chartData = null;
								allTmpKey = key + "_Date_Values";
								allTmpCats = all_d.getValue(allTmpKey);
								
								//this.dbgText.text += "\nafter all tmp key:" + allTmpKey + "|cats:" + allTmpCats+"\n";
								
								if (allTmpCats != null)
								{
									//Alert.show("cats:"+allTmpCats);
									all_calcData = allTmpCats.split(",");
									chartData = "";
									// loop through keys contatonating data
									for (j = 0; j < all_calcData.length;j++ )
									{
										allTmpKey = tempKey.substring(0,tempKey.indexOf(allString)) + all_calcData[j] + tempKey.substring(tempKey.indexOf(allString) + allString.length);
										//this.dbgText.text += "after all tmp data key val:" + allTmpKey;
										//Alert.show(allTmpKey);
										if (d.getValue(allTmpKey) != null)
										{
											chartData += ","+d.getValue(allTmpKey);
										}
										else
										{
											
											allTmpKey = tempAliasKey.substring(0,tempAliasKey.indexOf(allString)) + all_calcData[j] + tempAliasKey.substring(tempAliasKey.indexOf(allString) + allString.length);
											//this.dbgText.text += "after all tmp data key val:" + allTmpKey;
											//Alert.show(allTmpKey + ":" + tempAliasKey + ":" + allString );
											if (d.getValue(allTmpKey) != null)
											{
												chartData += ","+d.getValue(allTmpKey);
											}
										}
									}
									
									if (chartData == "")
									{
										chartData = null;
									}
								}
							}
							
							if (chartData != null)
							{
								//dataFound = true;
								//tempDict.Dates = chartData;
								
								// set the chart string data
								if (!this.chartHasCustomDatesSubChart)
								{
									dateDataFound = true;
									tempDict.Dates = chartData;
									randomArray = chartData.split(',');
									randomArray.reverse();
									//this.alChart.SetCategoryAxisDates(randomArray.join(','));
								
								}
								else
								{
									startDate = new Date();
									startDate.setTime(Date.parse(this.chartStartDate.label));
									
									endDate = new Date();
									endDate.setTime(Date.parse(this.chartEndDate.label));
									
									actualDate = new Date();
									allPossibleDates = chartData.split(',');
									newChartData = "";
									dateIndexes = new Array();
									
									for (a = 0; a < allPossibleDates.length; a++ )
									{
										date = allPossibleDates[a] as String;
										if (date != null)
										{
											//Alert.show(date+":"+startDate.toString()+":"+endDate.toString());
											try
											{
												actualDate.setTime(Date.parse(date));
												
												// is it in the range?
												if ((actualDate.getTime() >= startDate.getTime()) && (actualDate.getTime() <= endDate.getTime()))
												{
													newChartData += allPossibleDates[a] + ",";
													dateIndexes.push(a);
													//Alert.show("this is a:"+a);
												}
											}
											catch (err:Error)
											{
												Alert.show("[ERROR] problems loading custom dates:"+err.message);
											}
										}
									}
									
									dateDataFound = true;
									tempDict.Dates = newChartData;
									tempDict.DateIndexes = dateIndexes;
									//this.alChart.SetCategoryAxisDates(tempDict.Dates);
								}
							}
						}
						
						if (dateDataFound)
						{
							// save the date Key
							tempDateKey = tempKey;
							tempDateAliasKey = tempAliasKey;
						
							//Alert.show("-"+tempDateKey);
							//Alert.show("--" + tempDateAliasKey);
						}
						//Alert.show("dateFound now is:" + dateDataFound);
						// regular Dates W/O KPI
						if (!dateDataFound)
						{
							
							cbs = this.filterByComboBoxes[1];
							var tempNoKpiDateAliasKey:String = "";
							if ((key.indexOf("Mns") >= 0))
							{
								// if this is a date metric one
								tempNoKpiDateKey = key.substring(0, key.indexOf("Last_")) + monthName + key.substring(key.indexOf("_Mns") + 4);// + "_Date_Values";
								tempNoKpiDateKey = tempNoKpiDateKey.substring(0, tempNoKpiDateKey.indexOf(cbs.selectedLabel)) +  "Date_Values";
								
								tempNoKpiDateAliasKey = aliasKey.substring(0, aliasKey.indexOf("Last_")) + monthName + aliasKey.substring(aliasKey.indexOf("_Mns") + 4);// + "_Date_Values";
								tempNoKpiDateAliasKey = tempNoKpiDateAliasKey.substring(0, tempNoKpiDateAliasKey.indexOf(cbs.selectedLabel)) +  "Date_Values";
								//Alert.show("Key Val1:"+tempKey+"_Key val2:"+aliasKey);
								
								tempDateKeyIndex = tempNoKpiDateKey + "_Index";
								tempDateKeyIndexLength = tempNoKpiDateKey + "_Length";
								
								tempDateAliasKeyIndex = tempNoKpiDateAliasKey + "_Index";
								tempDateAliasKeyIndexLength = tempNoKpiDateAliasKey + "_Length";
								
								//Alert.show("indx:len="+tempDateKeyIndex+":"+tempDateKeyIndexLength);
							}
							else
							{
								if (key.indexOf("Last") >= 0)
								{
									// if this is a date metric one
									tempNoKpiDateKey = key.substring(0, key.indexOf("Last_")) + monthName;
									tempNoKpiDateKey+= key.substring(key.indexOf("Last_") + 5, key.indexOf(cbs.selectedLabel)) + "Date_Values";
								
									tempNoKpiDateAliasKey = aliasKey.substring(0, aliasKey.indexOf("Last_")) + monthName;
									tempNoKpiDateAliasKey+= aliasKey.substring(aliasKey.indexOf("Last_") + 5, aliasKey.indexOf(cbs.selectedLabel))  + "Date_Values";
								
									//Alert.show("Key Val1:"+tempKey+"_Key val2:"+aliasKey);
									//Alert.show("selectedLabel:"+cbs.selectedLabel)
									
									tempDateKeyIndex = tempNoKpiDateKey + "_Index";
									tempDateKeyIndexLength = tempNoKpiDateKey + "_Length";
									
									tempDateAliasKeyIndex = tempNoKpiDateAliasKey + "_Index";
									tempDateAliasKeyIndexLength = tempNoKpiDateAliasKey + "_Length";
									
									//Alert.show("indx:len=" + tempDateKeyIndex + ":" + tempDateKeyIndexLength);
									//Alert.show("indx:len="+tempDateAliasKeyIndex+":"+tempDateAliasKeyIndexLength+":key"+key+":alias key"+tempNoKpiDateAliasKey);
								}
								else
								{
									tempNoKpiDateKey = key.substring(0, key.indexOf(monthName)) + monthName;// + "_Date_Values";
									tempNoKpiDateKey = tempNoKpiDateKey.substring(0, tempNoKpiDateKey.indexOf(cbs.selectedLabel)) +  "Date_Values";
									
									tempNoKpiDateAliasKey = aliasKey.substring(0, aliasKey.indexOf(monthName)) + monthName;// + "_Date_Values";
									tempNoKpiDateAliasKey = tempNoKpiDateAliasKey.substring(0, tempNoKpiDateAliasKey.indexOf(cbs.selectedLabel)) +  "Date_Values";
									//Alert.show("Key Val2:"+tempNoKpiDateAliasKey);
								}
							}
							
							// use key to get the data
							if (!handlingAlls)
							{
								//if (!dateDataFound)
								{
									//Alert.show("DateKey:"+tempNoKpiDateKey+"-N-DateAliasKey:"+tempNoKpiDateAliasKey);
									// use key to get the data
									//tempDict.Labels = d.getValue("Month_Values");
									chartData = d.getValue(tempNoKpiDateKey);
									
									if (chartData == null)
									{
										chartData = d.getValue(tempNoKpiDateAliasKey);
										//Alert.show("was null now chartData:"+chartData+ ":" + tempNoKpiDateAliasKey);
									}
								
									if (chartData == null)
									{
										dateDataFound = false
									}
									else
									{
										dateDataFound = true;
									}
								}
							}
							else
							{
								chartData = null;
								allTmpKey = key + "_Date_Values";
								allTmpCats = all_d.getValue(allTmpKey);
								
								//this.dbgText.text += "\nafter all tmp key:" + allTmpKey + "|cats:" + allTmpCats+"\n";
								//Alert.show("Ciao:"+allTmpKey+":"+allTmpCats);
								if (allTmpCats != null)
								{
									var tmpDateCats:Array =  allTmpCats.split("|");
									
									if (tmpDateCats[0].indexOf(",") >= 0)
									{
										all_calcData = tmpDateCats[0].split(",");
									}
									else
									{
										all_calcData = tmpDateCats[1].split(",");
									}
									
									//Alert.show("b4dloop");
									chartData = "";
									// loop through keys contatonating data
									for (j = 0; j < all_calcData.length;j++ )
									{
										// try single all
										allTmpKey = tempNoKpiDateKey.substring(0,tempNoKpiDateKey.indexOf(allString)) + all_calcData[j] + tempNoKpiDateKey.substring(tempNoKpiDateKey.indexOf(allString) + allString.length);
										//this.dbgText.text += "after all tmp data key val:" + allTmpKey;
										//Alert.show(allTmpKey);
										if (d.getValue(allTmpKey) != null)
										{
											chartData += "," + d.getValue(allTmpKey);
											dateDataFound = true;
										}
										else
										{
											if (allString.lastIndexOf("_") == (allString.length-1))
											{
												allString = allString.substring(0,allString.length-1);
											}
											
											//TODO: fixHack
											if (all_calcData[j].indexOf("|")>=0)
											{
												all_calcData[j] = all_calcData[j].replace("|","");
											}
											
											allTmpKey = tempNoKpiDateAliasKey.substring(0,tempNoKpiDateAliasKey.indexOf(allString)) + all_calcData[j] + tempNoKpiDateAliasKey.substring(tempNoKpiDateAliasKey.indexOf(allString) + allString.length);
											
											if (d.getValue(allTmpKey + "_Index")!=null)
											{
												tempDict.DateStartIndex = d.getValue(allTmpKey + "_Index");
												tempDict.DateValueLength =  d.getValue(allTmpKey + "_Length");
												//Alert.show("key:"+allTmpKey+"index:" + tempDict.DateStartIndex + "Len:" + tempDict.DateValueLength);
											}
											
											//this.dbgText.text += "after all tmp data key val:" + allTmpKey;
											//Alert.show(allTmpKey + ":" + tempNoKpiDateAliasKey + ":" + allString );
											//Alert.show("index:"+tempDict.DateStartIndex+"Len:"+tempDict.DateValueLength);
											if ((d.getValue(allTmpKey) != null))
											{
												chartData += "," + d.getValue(allTmpKey);
												dateDataFound = true;
											}
											
											chartData = chartData.replace(",,", ",");
											//Alert.show("key:" + allTmpKey + "-Data:" + chartData);
											
											if ((!dateDataFound)&&(this.chartByLabels[0].text.indexOf("All_")>=0 || this.chartByLabels[0].text.indexOf("Some_")>=0)&&(this.chartByLabels[1].text.indexOf("All_")>=0))
											{
												// let the metrics hanlde it
												dateDataFound = true;
												//Alert.show("Found key:" + allTmpKey2+ ":"+allString2);
											}
										}
									}
									
									if (chartData == "")
									{
										chartData = null;
									}
								}
								else
								{
									// look up custom date stuff in the regular map
									if (this.chartHasCustomDatesSubChart)
									{
										/*
										Alert.show(key.substring(0, key.indexOf("Desk_")+5) + "Date_Values");
										// strip the KPI and get the data
										chartData = d.getValue(key.substring(0, key.indexOf("Desk_")+5) + "Date_Values");
										*/
										if ((this.chartByLabels[0].text.indexOf("Some_") >= 0) || (this.chartByLabels[0].text.indexOf("All_") >= 0))
										{
											allTmpCats = chartMetricAliasKeys.join(); 
											if (allTmpCats.indexOf("All_Prdct_Nms,") > 0)
											{
												allTmpCats = allTmpCats.substring(allTmpCats.indexOf("All_Prdct_Nms,") + 14);
											}
											
											if (allTmpCats.indexOf("Some_Prdct_Nms,") > 0)
											{
												allTmpCats = allTmpCats.substring(allTmpCats.indexOf("Some_Prdct_Nms,") + 15);
											}
										}
										
										if ((this.chartByLabels[1].text.indexOf("All_") >= 0))
										{
											allTmpCats = allChartByCategories.join();
											if (allTmpCats.indexOf(",All_PLs") > 0)
											{
												allTmpCats = allTmpCats.substring(0, allTmpCats.indexOf(",All_PLs"));
											}
										}
										//Alert.show("newAllCats:"+allTmpCats+":key"+key);
										// let the metrics hanlde it
										dateDataFound = true;
									}
								}
							}
							
							//Alert.show("new chart data:"+chartData);
							if (chartData != null)
							{
								//dataFound = true;
								//tempDict.Dates = chartData;
								
								// set the chart string data
								if (!this.chartHasCustomDatesSubChart)
								{
									dateDataFound = true;
									tempDict.Dates = chartData;
									randomArray = chartData.split(',');
									randomArray.reverse();
									//this.alChart.SetCategoryAxisDates(randomArray.join(','));
									
									//Alert.show("Alo");
									if (!handlingAlls)
									{
										//Alert.show("indx:lenb4="+tempDateKeyIndex+":"+tempDateKeyIndexLength);
										if (d.getValue(tempDateKeyIndex) != null)
										{
											tempDict.DateStartIndex = d.getValue(tempDateKeyIndex);
											//Alert.show(tempDateKeyIndex+":"+d.getValue(tempDateKeyIndex));
										}
										else
										{
											tempDict.DateStartIndex = d.getValue(tempDateAliasKeyIndex);
											//Alert.show(tempDateAliasKeyIndex+":"+d.getValue(tempDateAliasKeyIndex));
										}
										
										if (d.getValue(tempDateKeyIndexLength) != null)
										{
											tempDict.DateValueLength = d.getValue(tempDateKeyIndexLength);
										}
										else
										{
											tempDict.DateValueLength = d.getValue(tempDateAliasKeyIndexLength);
										}
										//Alert.show("indx:lenaftr="+tempDict.DateStartIndex+":"+tempDict.DateValueLength);
									}
								}
								else
								{
									startDate = new Date();
									startDate.setTime(Date.parse(this.chartStartDate.label));
									
									endDate = new Date();
									endDate.setTime(Date.parse(this.chartEndDate.label));
									
									actualDate = new Date();
									allPossibleDates = chartData.split(',');
									newChartData = "";
									dateIndexes = new Array();
									
									for (a = 0; a < allPossibleDates.length; a++ )
									{
										date = allPossibleDates[a] as String;
										if (date != null)
										{
											//Alert.show(date+":"+startDate.toString()+":"+endDate.toString());
											try
											{
												actualDate.setTime(Date.parse(date));
												
												// is it in the range?
												if ((actualDate.getTime() >= startDate.getTime()) && (actualDate.getTime() <= endDate.getTime()))
												{
													newChartData += allPossibleDates[a] + ",";
													dateIndexes.push(a);
													//Alert.show("this is a:"+a);
												}
											}
											catch (err:Error)
											{
												Alert.show("[ERROR] problems loading custom dates:"+err.message);
											}
										}
									}
									
									dateDataFound = true;
									tempDict.Dates = newChartData;
									tempDict.DateIndexes = dateIndexes;
									
									if (!handlingAlls)
									{
										//Alert.show("indx:lenb4="+tempDateKeyIndex+":"+tempDateKeyIndexLength);
										if (d.getValue(tempDateKeyIndex) != null)
										{
											tempDict.DateStartIndex = d.getValue(tempDateKeyIndex);
											//Alert.show(tempDateKeyIndex+":"+d.getValue(tempDateKeyIndex));
										}
										else
										{
											tempDict.DateStartIndex = d.getValue(tempDateAliasKeyIndex);
											//Alert.show(tempDateAliasKeyIndex+":"+d.getValue(tempDateAliasKeyIndex));
										}
										
										if (d.getValue(tempDateKeyIndexLength) != null)
										{
											tempDict.DateValueLength = d.getValue(tempDateKeyIndexLength);
										}
										else
										{
											tempDict.DateValueLength = d.getValue(tempDateAliasKeyIndexLength);
										}
										//Alert.show("indx:lenaftr="+tempDict.DateStartIndex+":"+tempDict.DateValueLength);
									}
									//this.alChart.SetCategoryAxisDates(tempDict.Dates);
								}
							}
						}
						
						if (dateDataFound)
						{
							// save the date Key
							tempDateKey = tempNoKpiDateKey;
							tempDateAliasKey = tempNoKpiDateAliasKey;
						
							//Alert.show("+"+tempDateKey);
							//Alert.show("++" + tempDateAliasKey);
						}
						
						//Alert.show("dateSIb4W/:"+tempDict.DateStartIndex);
						// METRIC VALUES W/ Month
						if (dateDataFound)
						{
							//Alert.show("DateKey:" + tempDateKey);
							//Alert.show("DateAliasKey:"+tempDateAliasKey);
							tempKey = key.substring(0, key.indexOf("Last_")) + monthName + key.substring(key.indexOf("_Mns")+4)+ "_Metric_Values";
							tempAliasKey = aliasKey.substring(0, aliasKey.indexOf("Last_")) + monthName + aliasKey.substring(aliasKey.indexOf("_Mns")+4)+ "_Metric_Values";
							
							if (tempDateKey == null)
							{
								tempDateKey = key.substring(0, key.indexOf("Last_")) + monthName + key.substring(key.indexOf("_Mns")+4)+ "_Date_Values";
								tempDateAliasKey = aliasKey.substring(0, aliasKey.indexOf("Last_")) + monthName + aliasKey.substring(aliasKey.indexOf("_Mns")+4)+ "_Date_Values";
							}
							
							// use key to get the data
							if (!handlingAlls)
							{
								// use key to get the data
								//tempDict.Labels = d.getValue("Month_Values");
								chartData = d.getValue(tempKey);
								
								if (chartData == null)
								{
									chartData = d.getValue(tempAliasKey);
								}
								
								if (chartData != null)
								{
									dataFound = true;
									//Alert.show("chartData!=null?"+chartData);
								}
								
								//this.dbgText.text += "\nALSO this is NOT a double all";
								//this.dbgText.text += "data key all str:" + tempKey;
								
								//Alert.show("*"+tempKey);
								//Alert.show("**" + tempAliasKey+":"+chartData);
							}
							else
							{
								//Alert.show("handling alls!");
								chartData = "";
								
								if (allTmpKey == null)
								{
									allTmpKey = key + "_Metric_Values";
								}
								
								if (allTmpDateKey == null)
								{
									allTmpDateKey = key + "_Date_Values";
								}
								
								if (allTmpCats == null)
								{
									allTmpCats = all_d.getValue(allTmpKey);
									allTmpDateCats = all_d.getValue(allTmpDateKey);
								}
								
								allString = "";
								//Alert.show("lookup key rslt for vals:" + allTmpCats + " & Dates:"+allTmpDateCats);
								//Alert.show("key for vals:" + tempKey + " & Dates:"+tempDateKey);
								//Alert.show("B4B4B4 allTmpKey for vals:" + allTmpKey + " & Dates:"+allTmpDateKey);
										
								if (allTmpCats != null)
								{
									all_calcData = allTmpCats.split(",");
								
									tempFreq = tempDict.Frequency;
									
									if (this.chartByLabels[0].text.indexOf("Some_")>=0)
									{
										// if there is an all string for cat 0 ,...Fix This Later!!
										//this.dbgText.text += "data key all 0 str:" + this.chartByLabels[0].text + "\n";
										
										allString += this.chartByLabels[0].text + "_";
									}
									
									if (this.chartByLabels[0].text.indexOf("All_")>=0)
									{
										// if there is an all string for cat 0 ,...Fix This Later!!
										//this.dbgText.text += "data key all 0 str:" + this.chartByLabels[0].text + "\n";
										
										allString += this.chartByLabels[0].text + "_";
									}
										
									if (this.chartByLabels[1].text.indexOf("All_")>=0)
									{
										// if there is an all string for cat 1 ,...Fix This Later!!
										//this.dbgText.text += "data key all 1 str:" + this.chartByLabels[1].text + "\n";
										
										allString += this.chartByLabels[1].text + "_";
									}
									
									/*
									if (this.chartByLabels[1].text.indexOf("All_")>=0)
									{
										// if there is an all string for cat 1 ,...Fix This Later!!
										//this.dbgText.text += "data key all 1 str:" + this.chartByLabels[1].text + "\n";
										
										allString += this.chartByLabels[1].text + "_";
									}
									*/
									// show all strng
									//this.dbgText.text += "data key all str:" + allString + "\n";
									//Alert.show("allSTring again:"+allString);
									
									// loop through keys contatonating data
									for (j = 0; j < all_calcData.length;j++ )
									{
										// show all strng
										//this.dbgText.text += "data key all str:" + all_calcData[j]+ "\n";
										//Alert.show("B4B4 allTmpKey for vals:" + allTmpKey + " & Dates:"+allTmpDateKey);
											
										
										if (((this.chartByLabels[0].text.indexOf("Some_") >= 0)&&(this.chartByLabels[1].text.indexOf("All_") >= 0))||((this.chartByLabels[0].text.indexOf("All_") >= 0)&&(this.chartByLabels[1].text.indexOf("All_") >= 0)))
										{
											//Alert.show("double All");
											//this.dbgText.text += "this is a double all0:" + all_calcData[j] + "\n";
											
											var all_calc_cats:Array = allTmpCats.split("|");
											
											
											for (k = 1; k < all_calc_cats.length;k++ )
											{
												var all_calc_cat_Data:Array = all_calc_cats[k].split(",");
												
												//this.dbgText.text += "this is a double all1:" + all_calc_cats[k] + "\n";
												
												if (all_calc_cat_Data != null)
												{
													for (var m:Number = 0; m < all_calc_cat_Data.length;m++ )
													{
														if (all_calc_cat_Data[m] != null)
														{
															if (!chartHasCustomDatesSubChart)
															{
																allTmpKey = tempKey.substring(0,tempKey.indexOf(allString)) + all_calcData[j] + "_" + all_calc_cat_Data[m] + "_"+ tempKey.substring(tempKey.indexOf(allString) + allString.length);
																//Alert.show("Key:"+allTmpKey)
																
																allTmpDateKey = tempDateKey.substring(0,tempDateKey.indexOf(allString)) + all_calcData[j] + "_" + all_calc_cat_Data[m] + "_"+ tempDateKey.substring(tempDateKey.indexOf(allString) + allString.length);
																//Alert.show("Key:"+allTmpKey+"-"+"DateKey:"+allTmpDateKey)
															}
															else
															{
																
															}
															
															// show all strng
															//this.dbgText.text += "data key all str:" + allTmpKey + "\n";
														
															if (d.getValue(allTmpKey) != null)
															{
																//Alert.show("non-bogus key:"+allTmpKey);
																//chartData +=  d.getValue(allTmpKey)+",";
																//tempFreq++; // per months
																//this.dbgText.text += "data key w/ data:\n" + allTmpKey +"=" + chartData;
																
																var o:Number = 0;
																while((o<allChartByData.length))
																{
																	allTempDict = allChartByData[o];
																	if (allTempDict != null)
																	{
																		allTempFreq = allTempDict.Frequency;
																		
																		this.dbgText.text += "\nfreq:"+allTempFreq+"dict:" + allTempDict.Chart_By + "=actual:" + all_calcData[j];
																		
																		//if (allTempDict.Chart_By == all_calcData[j])// category filter
																		//Alert.show("chartBy="+allTempDict.Chart_By_Alias+"-N-all Calc Data="+all_calcData[j]);
														
																		if ((allTempDict.Chart_By == all_calcData[j]) || (allTempDict.Chart_By_Alias == all_calcData[j]))
																		{
																			
																			try
																			{
																				//this.alChart.SetCategoryAxisDates(d.getValue(allTmpDateKey));
																				//this.alChart.SetCategoryAxisValues(d.getValue(allTmpKey));
																				
																				if (!this.chartHasCustomDatesSubChart)
																				{
																					
																					
																					//Alert.show("key:"+allTmpKey);
																					allTempDict.Metrics += d.getValue(allTmpKey) + ",";
																					allTempFreq++;
																					allTempDict.Frequency = allTempFreq;
																					this.dbgText.text += "*{"+allTempDict.Metrics+"}";
																					allChartByData[o] = allTempDict; // last All view
																					
																					//this.alChart.SetCategoryAxisDates(d.getValue(allTmpDateKey));
																					//this.alChart.SetCategoryAxisValues(allTempDict.Metrics);
																					break;
																				}
																				else
																				{
																					// do date data
																					chartData = d.getValue(allTmpDateKey);
																					//Alert.show("non-bogus date Key:"+allTmpDateKey);
																					
																					if (chartData != null)
																					{
																						// set the chart string data
																						if (this.chartHasCustomDatesSubChart)
																						{
																							//Alert.show("custom dates with data");
																					
																							startDate = new Date();
																							startDate.setTime(Date.parse(this.chartStartDate.label));
																							
																							endDate = new Date();
																							endDate.setTime(Date.parse(this.chartEndDate.label));
																							
																							actualDate = new Date();
																							allPossibleDates = chartData.split(',');
																							newChartData = "";
																							dateIndexes = new Array();
																							
																							for (a = 0; a < allPossibleDates.length; a++ )
																							{
																								date = allPossibleDates[a] as String;
																								if (date != null)
																								{
																									//Alert.show(date+":"+startDate.toString()+":"+endDate.toString());
																									try
																									{
																										actualDate.setTime(Date.parse(date));
																										
																										// is it in the range?
																										if ((actualDate.getTime() >= startDate.getTime()) && (actualDate.getTime() <= endDate.getTime()))
																										{
																											newChartData += allPossibleDates[a] + ",";
																											dateIndexes.push(a);
																											//Alert.show("this is a:"+a);
																										}
																									}
																									catch (err:Error)
																									{
																										Alert.show("[ERROR] problems loading custom dates:"+err.message);
																									}
																								}
																							}
																							
																							//dataFound = true;
																							tempDict.Dates = newChartData;
																							tempDict.DateIndexes = dateIndexes;
																							//Alert.show("DateIndexes going in:"+tempDict.DateIndexes);
																							//this.alChart.SetCategoryAxisDates(tempDict.Dates);
																						}
																					
																						
																				
																						// do the metricdata now
																						newChartData = "";
																						allPossibleMetrics = d.getValue(allTmpKey).split(',');
																						tempDict.DateIndexes
																						
																						if (dateIndexes.length == 0 )
																						{
																							//Alert.show("There Are No Data Sets Available For Selection(4)");
																						}
																						else
																						{
																							for (a = dateIndexes[0]; a <= dateIndexes[dateIndexes.length-1]; a++ )
																							{
																								//Alert.show("this is allPossibleMetrics:" + allPossibleMetrics[a]+"and a="+a);
																								
																								if ((allPossibleMetrics[a] != null))
																								{
																									newChartData += allPossibleMetrics[a] + ",";	
																									//Alert.show("this is newChartData:"+newChartData);
																								}
																							}
																						}
																						
																						allTempDict.Metrics += newChartData + ",";
																						allTempFreq++;
																						allTempDict.Frequency = allTempFreq;
																						this.dbgText.text += "*{"+allTempDict.Metrics+"}";
																						allChartByData[o] = allTempDict; // last All view
																						//this.alChart.SetCategoryAxisValues(allTempDict.Metrics);
																						break;
																					}
																				}
																			}
																			catch (err:Error)
																			{
																				Alert.show("[ERROR] problems loading all chart aggregates:"+err.getStackTrace());
																			}
																		}
																	}
																	
																	o++;
																}
															}
															else
															{
																//Alert.show("bogus key!");
																dataFound = false;
															}
														}
													}
												}
											}
										}
										else
										{
											this.dbgText.text += "this is NOT a double all";
											
											//Alert.show("#:"+tempKey);
											//Alert.show("##:"+tempDateKey);
											//Alert.show("allstr:"+allString);
											//Alert.show(all_calcData[j]);
											//Alert.show("B4B4 allTmpKey for vals:" + allTmpKey + " & Dates:"+allTmpDateKey);
											//if ((tempKey != null) && (tempDateKey != null))
											{
												allTmpKey = tempKey.substring(0,tempKey.indexOf(allString)) + all_calcData[j] + "_" + tempKey.substring(tempKey.indexOf(allString) + allString.length);
												allTmpKey = allTmpKey.replace("|", "");
											
												allTmpDateKey = tempDateKey.substring(0, tempDateKey.indexOf(allString)) + all_calcData[j] + "_" + tempDateKey.substring(tempDateKey.indexOf(allString) + allString.length);
												allTmpDateKey = allTmpDateKey.replace("|", "");
											}
											
											//else
											//{
												//Alert.show("AffTr allTmpKey for vals:" + allTmpKey + " & Dates:"+allTmpDateKey);
												//Alert.show("AffTr allTmpKey for vals:" + tempKey + " & Dates:"+tempDateKey);
											//}
											// show all strng
											//this.dbgText.text += "data key all str:" + allTmpKey + "\n";
											
											if (d.getValue(allTmpKey) != null)
											{
												chartData +=  d.getValue(allTmpKey)+",";
												tempFreq++; // per months?
												//Alert.show("data found here");
											}
											else
											{
												//Alert.show("no data found with metric month");
											}
											
											if (d.getValue(allTmpKey) != null)
											{
												//Alert.show("data found here TOO?!?!@#");
												//this.dbgText.text += "data key w/ data:\n" + allTmpKey +"="+chartData;
												
												//for (j = 0; j < allChartByData.length; j++ )
												// find the matching value (shift,operator)
												k = 0;
												while((k<allChartByData.length))
												{
													allTempDict = allChartByData[k];
													if (allTempDict != null)
													{
														allTempFreq = allTempDict.Frequency;
														
														this.dbgText.text += "\n>dict:" + allTempDict.Chart_By + "=actual:" + all_calcData[j];
														
														//if (allTempDict.Chart_By == all_calcData[j])
														//Alert.show("chartBy="+allTempDict.Chart_By_Alias+"-N-all Calc Data="+all_calcData[j]);
														
														if ((allTempDict.Chart_By == all_calcData[j]) || (allTempDict.Chart_By_Alias == all_calcData[j]))
														{
															//allTempDict.Metrics += d.getValue(allTmpKey) + ",";
															//allTempFreq++;
															//allTempDict.Frequency = allTempFreq;
															//this.dbgText.text += "*{"+allTempDict.Metrics+"}";
															//allChartByData[k] = allTempDict; // last All view
															//break;
															
															//this.alChart.SetCategoryAxisValues(d.getValue(allTmpKey));
															
															if (!this.chartHasCustomDatesSubChart)
															{
																//this.alChart.SetCategoryAxisDates(d.getValue(allTmpDateKey));
																
																allTempDict.Metrics += d.getValue(allTmpKey) + ",";
																allTempFreq++;
																allTempDict.Frequency = allTempFreq;
																this.dbgText.text += "*{"+allTempDict.Metrics+"}";
																allChartByData[k] = allTempDict; // last All view
																//this.alChart.SetCategoryAxisValues(allTempDict.Metrics);
																break;
															}
															else
															{
																chartData  = d.getValue(allTmpDateKey);
																
																//Alert.show("non-bogus Key:"+allTmpKey);
																//Alert.show("non-bogus date Key:"+allTmpDateKey);
																
																if (chartData != null)
																{
																	// set the chart string dates first
																	if (this.chartHasCustomDatesSubChart)
																	{
																		//Alert.show("custom dates with data");
																
																		startDate = new Date();
																		startDate.setTime(Date.parse(this.chartStartDate.label));
																		
																		endDate = new Date();
																		endDate.setTime(Date.parse(this.chartEndDate.label));
																		
																		actualDate = new Date();
																		allPossibleDates = chartData.split(',');
																		newChartData = "";
																		dateIndexes = new Array();
																		
																		for (a = 0; a < allPossibleDates.length; a++ )
																		{
																			date = allPossibleDates[a] as String;
																			if (date != null)
																			{
																				//Alert.show(date+":"+startDate.toString()+":"+endDate.toString());
																				try
																				{
																					actualDate.setTime(Date.parse(date));
																					
																					// is it in the range?
																					if ((actualDate.getTime() >= startDate.getTime()) && (actualDate.getTime() <= endDate.getTime()))
																					{
																						newChartData += allPossibleDates[a] + ",";
																						dateIndexes.push(a);
																						//Alert.show("this is a:"+a);
																					}
																				}
																				catch (err:Error)
																				{
																					Alert.show("[ERROR] problems loading custom dates:"+err.message);
																				}
																			}
																		}
																		
																		//dataFound = true;
																		tempDict.Dates = newChartData;
																		tempDict.DateIndexes = dateIndexes;
																		//Alert.show("DateIndexes going in:"+tempDict.DateIndexes);
																		//this.alChart.SetCategoryAxisDates(tempDict.Dates);
																	}
															
																	//this.alChart.SetCategoryAxisDates(tempDict.Dates);
																	
																	// do the data next
																	newChartData = "";
																	allPossibleMetrics = d.getValue(allTmpKey).split(',');
																	tempDict.DateIndexes
																	
																	if (dateIndexes.length == 0 )
																	{
																		//Alert.show("There Are No Data Sets Available For Selection(4)");
																	}
																	else
																	{
																		for (a = dateIndexes[0]; a <= dateIndexes[dateIndexes.length-1]; a++ )
																		{
																			//Alert.show("this is allPossibleMetrics:" + allPossibleMetrics[a]+"and a="+a);
																			
																			if ((allPossibleMetrics[a] != null))
																			{
																				newChartData += allPossibleMetrics[a] + ",";	
																				//Alert.show("this is newChartData:"+newChartData);
																			}
																		}
																	}
																	
																	allTempDict.Metrics += newChartData + ",";
																	allTempFreq++;
																	allTempDict.Frequency = allTempFreq;
																	this.dbgText.text += "*{"+allTempDict.Metrics+"}";
																	allChartByData[k] = allTempDict; // last All view
																	//this.alChart.SetCategoryAxisValues(allTempDict.Metrics);
																	break;
																}
															}
														}
													}
													else
													{
														//this.dbgText.text += "...k="+k+"isnull"
													}
													
													k++;
												}
											}
											else
											{
												
												// aliases
												{
												allTmpKey = tempAliasKey.substring(0,tempAliasKey.indexOf(allString)) + all_calcData[j] + "_" + tempAliasKey.substring(tempAliasKey.indexOf(allString) + allString.length);
												allTmpKey = allTmpKey.replace("|", "");
											
												allTmpDateKey = tempDateAliasKey.substring(0, tempDateAliasKey.indexOf(allString)) + all_calcData[j] + "_" + tempDateAliasKey.substring(tempDateAliasKey.indexOf(allString) + allString.length);
												allTmpDateKey = allTmpDateKey.replace("|", "");
												}
												
												//else
												//{
													//Alert.show("AffTr allTmpKey for vals:" + allTmpKey + " & Dates:"+allTmpDateKey);
													//Alert.show("AffTr allTmpKey for vals:" + tempKey + " & Dates:"+tempDateKey);
												//}
												// show all strng
												//this.dbgText.text += "data key all str:" + allTmpKey + "\n";
												
												if (d.getValue(allTmpKey) != null)
												{
													chartData +=  d.getValue(allTmpKey)+",";
													tempFreq++; // per months?
													//Alert.show("data found here");
												}
												else
												{
													//Alert.show("no data found with metric month");
												}
												
												if (d.getValue(allTmpKey) != null)
												{
													//Alert.show("data found here TOO?!?!@#");
													//this.dbgText.text += "data key w/ data:\n" + allTmpKey +"="+chartData;
													
													// find the matching value (shift,operator)
													//for (j = 0; j < allChartByData.length; j++ )
													k = 0;
													while((k<allChartByData.length))
													{
														allTempDict = allChartByData[k];
														if (allTempDict != null)
														{
															allTempFreq = allTempDict.Frequency;
															
															this.dbgText.text += "\n>dict:" + allTempDict.Chart_By + "=actual:" + all_calcData[j];
															
															//if (allTempDict.Chart_By == all_calcData[j])
															//Alert.show("chartBy="+allTempDict.Chart_By_Alias+"-N-all Calc Data="+all_calcData[j]);
															
															if ((allTempDict.Chart_By == all_calcData[j]) || (allTempDict.Chart_By_Alias == all_calcData[j]))
															{
																//allTempDict.Metrics += d.getValue(allTmpKey) + ",";
																//allTempFreq++;
																//allTempDict.Frequency = allTempFreq;
																//this.dbgText.text += "*{"+allTempDict.Metrics+"}";
																//allChartByData[k] = allTempDict; // last All view
																//break;
															
																//this.alChart.SetCategoryAxisDates(d.getValue(allTmpDateKey));
																//this.alChart.SetCategoryAxisValues(d.getValue(allTmpKey));
																
																if (!this.chartHasCustomDatesSubChart)
																{
																	
																	
																	allTempDict.Metrics += d.getValue(allTmpKey) + ",";
																	allTempFreq++;
																	allTempDict.Frequency = allTempFreq;
																	this.dbgText.text += "*{"+allTempDict.Metrics+"}";
																	allChartByData[k] = allTempDict; // last All view
																	//this.alChart.SetCategoryAxisValues (allTempDict.Metrics);
																	break;
																}
																else
																{
																	chartData  = d.getValue(allTmpDateKey);
																	
																	//Alert.show("non-bogus Key:"+allTmpKey);
																	//Alert.show("non-bogus date Key:"+allTmpDateKey);
																	
																	if (chartData != null)
																	{
																		// set the chart string dates first
																		if (this.chartHasCustomDatesSubChart)
																		{
																			//Alert.show("custom dates with data");
																	
																			startDate = new Date();
																			startDate.setTime(Date.parse(this.chartStartDate.label));
																			
																			endDate = new Date();
																			endDate.setTime(Date.parse(this.chartEndDate.label));
																			
																			actualDate = new Date();
																			allPossibleDates = chartData.split(',');
																			newChartData = "";
																			dateIndexes = new Array();
																			
																			for (a = 0; a < allPossibleDates.length; a++ )
																			{
																				date = allPossibleDates[a] as String;
																				if (date != null)
																				{
																					//Alert.show(date+":"+startDate.toString()+":"+endDate.toString());
																					try
																					{
																						actualDate.setTime(Date.parse(date));
																						
																						// is it in the range?
																						if ((actualDate.getTime() >= startDate.getTime()) && (actualDate.getTime() <= endDate.getTime()))
																						{
																							newChartData += allPossibleDates[a] + ",";
																							dateIndexes.push(a);
																							//Alert.show("this is a:"+a);
																						}
																					}
																					catch (err:Error)
																					{
																						Alert.show("[ERROR] problems loading custom dates:"+err.message);
																					}
																				}
																			}
																			
																			//dataFound = true;
																			tempDict.Dates = newChartData;
																			tempDict.DateIndexes = dateIndexes;
																			//Alert.show("DateIndexes going in:"+tempDict.DateIndexes);
																			//this.alChart.SetCategoryAxisDates(tempDict.Dates);
																		}
																	
																		
																		
																		// do the data next
																		newChartData = "";
																		allPossibleMetrics = d.getValue(allTmpKey).split(',');
																		tempDict.DateIndexes
																		
																		if (dateIndexes.length == 0 )
																		{
																			//Alert.show("There Are No Data Sets Available For Selection(4)");
																		}
																		else
																		{
																			for (a = dateIndexes[0]; a <= dateIndexes[dateIndexes.length-1]; a++ )
																			{
																				//Alert.show("this is allPossibleMetrics:" + allPossibleMetrics[a]+"and a="+a);
																				
																				if ((allPossibleMetrics[a] != null))
																				{
																					newChartData += allPossibleMetrics[a] + ",";	
																					//Alert.show("this is newChartData:"+newChartData);
																				}
																			}
																		}
																		
																		allTempDict.Metrics += newChartData + ",";
																		allTempFreq++;
																		allTempDict.Frequency = allTempFreq;
																		this.dbgText.text += "*{"+allTempDict.Metrics+"}";
																		allChartByData[k] = allTempDict; // last All view
																		//this.alChart.SetCategoryAxisValues(allTempDict.Metrics);
																		break;
																	}
																}
															}
														}
														else
														{
															//this.dbgText.text += "...k="+k+"isnull"
														}
														
														k++;
													}
												}
											}
										}
									}
								}
							}
						}
						
						//Alert.show("Date should be found:"+dateDataFound+"N data should NOT be found:"+dataFound);
						//Alert.show("dateSIb4W/O:"+tempDict.DateStartIndex);
						//Alert.show("mon:"+monthName);
						// METRIC VALUES W/O Month
						if ((dateDataFound))
						{
							//Alert.show("date found but no data yet:"+monthName);
							//tempKey = key.substring(0, key.indexOf("Last_")) + monthName + key.substring(key.indexOf("_Mns") + 4) + "_Metric_Values";
							// no month metric
							tempNoMonthKey = rawDatesKey + "_Metric_Values";
							//Alert.show("raw :"+rawDatesKey);
							//Alert.show("raw Alias:"+rawDatesAliasKey);
							tempNoMonthAliasKey = rawDatesAliasKey + "_Metric_Values";
							
							// use key to get the data
							if (!handlingAlls)
							{
								// use key to get the data
								//tempDict.Labels = d.getValue("Month_Values");
								chartData = d.getValue(tempNoMonthKey);
								
								if (chartData == null)
								{
									if (tempDict.DateStartIndex != null)
									{
										chartData = "";
										//Alert.show("validMetrics="+d.getValue(tempNoMonthAliasKey)+":key="+tempNoMonthAliasKey);
										if (d.getValue(tempNoMonthAliasKey) != null)
										{
											//Alert.show("validMetrics="+d.getValue(tempNoMonthAliasKey)+":key="+tempNoMonthAliasKey);
											validMetrics = d.getValue(tempNoMonthAliasKey).split(",");
											dateStartIndexes = tempDict.DateStartIndex.split(",");
											dateValueLengths = tempDict.DateValueLength.split(",");
											
											for (y = 0; y < dateValueLengths.length; y++  )
											{
												dateStartIndex = dateStartIndexes[y];
												dateValueLength = dateValueLengths[y];
												cntr = new Number(0);
												for (z = 0; z < validMetrics.length; z++ )
												{
													if ((z >= dateStartIndex)&&(cntr<=dateValueLength))
													{
														chartData +=  validMetrics[z] + ",";
														cntr++;
													}
												}
											}
											
											chartData = chartData.substring(0,chartData.lastIndexOf(","));
											//Alert.show("data:"+chartData+":"+validMetrics+":"+dateStartIndexes+":"+dateValueLengths);
										}
									}
									else
									{
										chartData = d.getValue(tempNoMonthAliasKey);
									}
								}
								
								if (chartData != null)
								{
									dataFound = true;
								}
								
								//this.dbgText.text += "\nALSO this is NOT a double all";
								//this.dbgText.text += "data key all str:" + tempNoMonthKey;
								
								//Alert.show("/"+tempNoMonthKey);
								//Alert.show("//" + tempNoMonthAliasKey);
							}
							else
							{
								//Alert.show("handling alls!:"+monthName);
								
								chartData = "";
								
								if (allTmpKey == null)
								{
									allTmpKey = key + "_Metric_Values";
								}
								
								if (allTmpDateKey == null)
								{
									allTmpDateKey = key + "_Date_Values";
								}
								
								if (allTmpCats == null)
								{
									allTmpCats = all_d.getValue(allTmpKey);
									allTmpDateCats = all_d.getValue(allTmpDateKey);
								}
								
								//Alert.show("allTmp:" + allTmpKey+"N allTmpDate"+ allTmpDateKey+":cats"+allTmpCats);
								//Alert.show("allTmpCats:"+allTmpCats);
								
								allString = "";
										
								if (allTmpCats != null)
								{
									//Alert.show("we're in there!");
									all_calcData = allTmpCats.split(",");
								
									tempFreq = tempDict.Frequency;
									
									if (this.chartByLabels[0].text.indexOf("Some_")>=0)
									{
										// if there is an all string for cat 0 ,...Fix This Later!!
										//this.dbgText.text += "data key all 0 str:" + this.chartByLabels[0].text + "\n";
										
										allString += this.chartByLabels[0].text + "_";
									}
									
									if (this.chartByLabels[0].text.indexOf("All_")>=0)
									{
										// if there is an all string for cat 0 ,...Fix This Later!!
										//this.dbgText.text += "data key all 0 str:" + this.chartByLabels[0].text + "\n";
										
										allString += this.chartByLabels[0].text + "_";
									}
										
									if (this.chartByLabels[1].text.indexOf("All_")>=0)
									{
										// if there is an all string for cat 1 ,...Fix This Later!!
										//this.dbgText.text += "data key all 1 str:" + this.chartByLabels[1].text + "\n";
										
										allString += this.chartByLabels[1].text + "_";
									}
									
									// show all strng
									//this.dbgText.text += "data key all str:" + allString + "\n";
									//Alert.show("allstr:"+allString);
									
									
									// loop through keys contatonating data
									for (j = 0; j < all_calcData.length;j++ )
									{
										// show all strng
										//this.dbgText.text += "data key all str:" + all_calcData[j]+ "\n";
										//Alert.show("B4B4 allTmpKey for vals:" + allTmpKey + " & Dates:"+allTmpDateKey);
										
										
										if (((this.chartByLabels[0].text.indexOf("Some_") >= 0)&&(this.chartByLabels[1].text.indexOf("All_") >= 0))||((this.chartByLabels[0].text.indexOf("All_") >= 0)&&(this.chartByLabels[1].text.indexOf("All_") >= 0)))
										{
											//Alert.show("double all!");
											//this.dbgText.text += "this is a double all0:" + all_calcData[j] + "\n";
											
											all_calc_cats = allTmpCats.split("|");
											//Alert.show("acc:"+all_calc_cats[1]+"-"+all_calcData[0]);
											
											for (k = 1; k < all_calc_cats.length;k++ )
											{
												all_calc_cat_Data = all_calc_cats[k].split(",");
												
												//this.dbgText.text += "this is a double all1:" + all_calc_cats[k] + "\n";
												
												if (all_calc_cat_Data != null)
												{
													for (m = 0; m < all_calc_cat_Data.length;m++ )
													{
														if ((all_calc_cat_Data[m] != null)&&(all_calcData[j]!=all_calc_cat_Data[m]))
														{
															
															//allTmpKey = tempKey.substring(0,tempKey.indexOf(allString)) + all_calcData[j] + "_" + all_calc_cat_Data[m] + "_"+ tempKey.substring(tempKey.indexOf(allString) + allString.length);
															//Alert.show("*Key:"+allTmpKey)
															
															//allTmpDateKey = tempDateKey.substring(0,tempDateKey.indexOf(allString)) + all_calcData[j] + "_" + all_calc_cat_Data[m] + "_"+ tempDateKey.substring(tempDateKey.indexOf(allString) + allString.length);
															//Alert.show("DateKey:"+allTmpDateKey)
															if (!chartHasCustomDatesSubChart)
															{
																allTmpKey = tempNoMonthKey.substring(0,tempNoMonthKey.indexOf(allString)) + all_calcData[j] + "_" + all_calc_cat_Data[m] + "_" + tempNoMonthKey.substring(tempNoMonthKey.indexOf(allString) + allString.length);
																allTmpKey = allTmpKey.replace("|", "");
																
																allTmpDateKey = tempNoKpiDateAliasKey.substring(0, tempNoKpiDateAliasKey.indexOf(allString)) + all_calcData[j] + "_" + all_calc_cat_Data[m] + "_" + tempNoKpiDateAliasKey.substring(tempNoKpiDateAliasKey.indexOf(allString) + allString.length);
																allTmpDateKey = allTmpDateKey.replace("|", "");
																//Alert.show("1:"+all_calcData[j] +" N "+all_calc_cat_Data[m]+"then->"+allTmpKey);
															}
															else
															{
																if ((key.indexOf(allString)>=0)&&(key.indexOf("Last_")<0))
																{
																	allTmpKey = key.substring(0,key.indexOf(allString)) + all_calcData[j] + "_" + all_calc_cat_Data[m] + "_" + key.substring(key.indexOf(allString) + (allString.length));
																	allTmpKey = allTmpKey.substring(0, allTmpKey.indexOf(monthName)-1) + allTmpKey.substring(allTmpKey.indexOf(monthName) + monthName.length) + "_Metric_Values";
																	
																	allTmpDateKey = key.substring(0, key.indexOf(allString)) + all_calcData[j] + "_" + all_calc_cat_Data[m] + "_" + key.substring(key.indexOf(allString) + (allString.length));
																	allTmpDateKey = allTmpDateKey.substring(0, allTmpDateKey.indexOf(this.chartFilter.selectedItem as String)) + "Date_Values";
																	//Alert.show("first:"+allTmpKey);
																}
																
																if ((key.indexOf(allString)>=0)&&(key.indexOf("Last_")>0))
																{
																	allTmpKey = key.substring(0,key.indexOf(allString)) + all_calcData[j] + "_" + all_calc_cat_Data[m] + "_" + key.substring(key.indexOf(allString) + (allString.length));
																	allTmpKey = allTmpKey.substring(0, allTmpKey.indexOf("Last")) + allTmpKey.substring(allTmpKey.indexOf("Last") + 6) + "_Metric_Values";
																	
																	allTmpDateKey = key.substring(0, key.indexOf(allString)) + all_calcData[j] + "_" + all_calc_cat_Data[m] + "_" + key.substring(key.indexOf(allString) + (allString.length));
																	allTmpDateKey = allTmpDateKey.substring(0, allTmpDateKey.indexOf(this.chartFilter.selectedItem as String)) 
																	allTmpDateKey = allTmpDateKey.substring(0, allTmpDateKey.indexOf("Last")) + monthName + "_" + allTmpDateKey.substring(allTmpDateKey.indexOf("Last") + 6) + "Date_Values";
																	
																	//Alert.show("second");
																	//Alert.show("AffTr allTmpKey for vals:" + allTmpKey + " & Dates:"+allTmpDateKey+":"+all_calcData[j]+":"+allString+":"+monthName+":"+key);
																}
															}
															
															// show all strng
															//this.dbgText.text += "data key all str:" + allTmpKey + "\n";
															//Alert.show("tmp key:"+tempNoMonthKey+":"+"tmp date:"+allTmpDateKey);
															//Alert.show("tmp key:" + all_calcData[j] + ":" + all_calc_cat_Data[m] + tempKey);
															//Alert.show( "data key all str:" + allTmpKey );
															
															if (d.getValue(allTmpKey) != null)
															{
																//Alert.show("non-bogus key:"+allTmpKey+":"+"non-bogus date key:"+allTmpDateKey);
																//chartData +=  d.getValue(allTmpKey)+",";
																//tempFreq++; // per months
																//this.dbgText.text += "data key w/ data:\n" + allTmpKey +"=" + chartData;
																
																o = 0;
																while((o<allChartByData.length))
																{
																	allTempDict = allChartByData[o];
																	if (allTempDict != null)
																	{
																		allTempFreq = allTempDict.Frequency;
																		
																		//this.dbgText.text += "\nfreq:"+allTempFreq+"dict:" + allTempDict.Chart_By + "=actual:" + all_calcData[j];
																		
																		//Alert.show("chartBy="+allTempDict.Chart_By+"-N-all Calc Data="+all_calcData[j]);
														
																		//if (allTempDict.Chart_By == all_calcData[j])// category filter
																		if ((allTempDict.Chart_By == all_calcData[j])||(allTempDict.Chart_By_Alias == all_calcData[j]))
																		{
																			
																			try
																			{
																				//this.alChart.SetCategoryAxisDates(d.getValue(allTmpDateKey));
																				//this.alChart.SetCategoryAxisValues(d.getValue(allTmpKey));
															
																				if (!this.chartHasCustomDatesSubChart)
																				{	
																					//this.alChart.SetCategoryAxisDates(d.getValue(allTmpDateKey));
																					//this.alChart.SetCategoryAxisValues(d.getValue(allTmpKey));
																					
																					// set start end end indexes
																					allTempDict.DateStartIndex = d.getValue(allTmpDateKey + "_Index");
																					allTempDict.DateValueLength = d.getValue(allTmpDateKey + "_Length");
																					
																					//Alert.show("key:"+allTmpKey+"-Mon:"+monthName+"-DSI:"+allTempDict.DateStartIndex+"-DVL:"+allTempDict.DateValueLength);
																					if (allTempDict.DateStartIndex != null)
																					{
																						if ((allTempDict.DateStartIndex.length >0))
																						{
																							//Alert.show(tempDict.DateValueLength.toString());
																							validMetrics = d.getValue(allTmpKey).split(",");
																							chartData = "";
																							cntr = new Number(0);
																							dateStartIndexes = allTempDict.DateStartIndex.split(",");
																							dateValueLengths = allTempDict.DateValueLength.split(",");
																							monthMetrics = new Array();
																							for (y = 0; y < dateValueLengths.length; y++  )
																							{
																								dateStartIndex = dateStartIndexes[y];
																								dateValueLength = dateValueLengths[y];
																								//Alert.show(allTmpKey+"startIndx_:" + dateStartIndex + "&length:" + dateValueLength);
																								cntr = 0;
																								for (z = 0; z < validMetrics.length; z++ )
																								{
																									if ((z >= dateStartIndex)&&(cntr<=dateValueLength))
																									{
																										//chartData +=  validMetrics[z] + ",";
																										monthMetrics.push(validMetrics[z]);
																										//Alert.show(validMetrics[z]);
																										cntr++;
																									}
																								}
																							}
																							
																							//Alert.show(allTmpKey+":"+monthMetrics.toString()+":"+dateValueLength);
																							//Alert.show("chartBy:"+allTempDict.Chart_By_Alias+"-Month:"+monthName+"-Metrics:"+monthMetrics);
																							if (monthMetrics.length >= 1)
																							{
																								allTempDict.Metrics += "," +monthMetrics.toString();
																								allTempDict.Metrics = allTempDict.Metrics.replace(",,", ",");
																							}
																							//Alert.show("chartBy:"+allTempDict.Chart_By_Alias+"-Metrics so far:"+allTempDict.Metrics);
																							
																							
																						}
																						//Alert.show("We Have Date Index"+allTmpKey);
																					}
																										
																					if (allTempDict.Metrics.indexOf(",") == 0)
																					{
																						allTempDict.Metrics = allTempDict.Metrics.substring(1);
																					}
																					
																					if (allTempDict.Metrics.lastIndexOf(",") == allTempDict.Metrics.length - 1)
																					{
																						allTempDict.Metrics = allTempDict.Metrics.substring(0, allTempDict.Metrics.length - 1);
																					}
																					
																					//this.alChart.SetCategoryAxisValues(allTempDict.Metrics);
																					
																					allTempFreq++;
																					allTempDict.Frequency = allTempFreq;
																					//this.dbgText.text += "*{"+allTempDict.Metrics+"}";
																					allChartByData[o] = allTempDict; // last All view
																					//dataFound = true;
																					break;
																				}
																				else
																				{
																					// do date data
																					chartData = d.getValue(allTmpDateKey);
																					//Alert.show("non-bogus date Key:"+allTmpDateKey);
																					
																					if (chartData != null)
																					{
																						// set the chart string data
																						if (this.chartHasCustomDatesSubChart)
																						{
																							//Alert.show("custom dates with data");
																					
																							startDate = new Date();
																							startDate.setTime(Date.parse(this.chartStartDate.label));
																							
																							endDate = new Date();
																							endDate.setTime(Date.parse(this.chartEndDate.label));
																							
																							actualDate = new Date();
																							allPossibleDates = chartData.split(',');
																							newChartData = "";
																							dateIndexes = new Array();
																							
																							for (a = 0; a < allPossibleDates.length; a++ )
																							{
																								date = allPossibleDates[a] as String;
																								if (date != null)
																								{
																									//Alert.show(date+":"+startDate.toString()+":"+endDate.toString());
																									try
																									{
																										actualDate.setTime(Date.parse(date));
																										
																										// is it in the range?
																										if ((actualDate.getTime() >= startDate.getTime()) && (actualDate.getTime() <= endDate.getTime()))
																										{
																											newChartData += allPossibleDates[a] + ",";
																											dateIndexes.push(a);
																											//Alert.show("this is a:"+a);
																										}
																									}
																									catch (err:Error)
																									{
																										Alert.show("[ERROR] problems loading custom dates:"+err.message);
																									}
																								}
																							}
																							
																							dataFound = true;
																							tempDict.Dates = newChartData;
																							tempDict.DateIndexes = dateIndexes;
																							//Alert.show("DateIndexes going in:"+tempDict.DateIndexes);
																							//this.alChart.SetCategoryAxisDates(tempDict.Dates);
																						}
																					
																						
																						
																						// do the metricdata now
																						newChartData = "";
																						allPossibleMetrics = d.getValue(allTmpKey).split(',');
																						tempDict.DateIndexes
																						
																						if (dateIndexes.length == 0 )
																						{
																							//Alert.show("There Are No Data Sets Available For Selection(4)");
																						}
																						else
																						{
																							for (a = dateIndexes[0]; a <= dateIndexes[dateIndexes.length-1]; a++ )
																							{
																								//Alert.show("this is allPossibleMetrics:" + allPossibleMetrics[a]+"and a="+a);
																								
																								if ((allPossibleMetrics[a] != null))
																								{
																									newChartData += allPossibleMetrics[a] + ",";	
																									//Alert.show("this is newChartData:"+newChartData);
																								}
																							}
																						}
																						
																						allTempDict.Metrics += newChartData + ",";
																						allTempFreq++;
																						allTempDict.Frequency = allTempFreq;
																						this.dbgText.text += "*{"+allTempDict.Metrics+"}";
																						allChartByData[o] = allTempDict; // last All view
																						//this.alChart.SetCategoryAxisValues(allTempDict.Metrics);
																						break;
																					}
																				}
																			}
																			catch (err:Error)
																			{
																				Alert.show("[ERROR] problems loading all chart aggregates:"+err.getStackTrace());
																			}
																		}
																		else
																		{
																			//Alert.show("tricky else case! for custom dates");
																			//Alert.show("no chart by m1:"+monthName+" m2:"+allTempDict.Month+"ChartBy:"+allTempDict.ChartBy);
																			if(chartHasCustomDatesSubChart&&(monthName==allTempDict.Month))
																			{
																				chartData  = d.getValue(allTmpDateKey);
																				//allTempDict.ChartBy = all_calcData[j];
																				//Alert.show("non-bogus Key:"+allTmpKey);
																				//Alert.show("non-bogus date Key:"+allTmpDateKey);
																				
																				if (chartData != null)
																				{
																					// set the chart string dates first
																					if (this.chartHasCustomDatesSubChart)
																					{
																						//Alert.show("custom dates with data");
																				
																						startDate = new Date();
																						startDate.setTime(Date.parse(this.chartStartDate.label));
																						
																						endDate = new Date();
																						endDate.setTime(Date.parse(this.chartEndDate.label ));
																						
																						actualDate = new Date();
																						allPossibleDates = chartData.split(',');
																						newChartData = "";
																						dateIndexes = new Array();
																						
																						var baseIndx:Number = new Number(d.getValue(allTmpDateKey + "_Index"));
																						
																						for (a = 0; a < allPossibleDates.length; a++ )
																						{
																							date = allPossibleDates[a] as String;
																							if (date != null)
																							{
																								//Alert.show(date+":"+startDate.toString()+":"+endDate.toString());
																								try
																								{
																									actualDate.setTime(Date.parse(date));
																									
																									// is it in the range?
																									if ((actualDate.getTime() >= startDate.getTime()) && (actualDate.getTime() <= endDate.getTime()))
																									{
																										newChartData += allPossibleDates[a] + ",";
																										dateIndexes.push(a+baseIndx);
																										//Alert.show("this is a:"+a);
																									}
																								}
																								catch (err:Error)
																								{
																									Alert.show("[ERROR] problems loading custom dates:"+err.message);
																								}
																							}
																						}
																						
																						dataFound = true;
																						tempDict.Dates = newChartData;
																						tempDict.DateIndexes = dateIndexes;
																						//tempDict.Month = monthName;
																						//allTempDict.Dates = newChartData;
																						//allTempDict.DateIndexes = dateIndexes;
																						//allTempDict.Month = monthName;
																						//Alert.show("DateIndexes going in:"+tempDict.DateIndexes);
																						//Alert.show("Dates going in:"+tempDict.Dates+" mon:"+tempDict.Month);
																						//this.alChart.SetCategoryAxisDates(tempDict.Dates);
																					}
																				
																					
																					
																					// do the data next
																					newChartData = "";
																					allPossibleMetrics = d.getValue(allTmpKey).split(',');
																					//tempDict.DateIndexes
																					
																					if (dateIndexes.length == 0 )
																					{
																						//Alert.show("There Are No Data Sets Available For Selection(4)");
																					}
																					else
																					{
																						for (a = dateIndexes[0]; a <= dateIndexes[dateIndexes.length-1]; a++ )
																						{
																							//Alert.show("this is allPossibleMetrics:" + allPossibleMetrics[a]+"and a="+a);
																							
																							if ((allPossibleMetrics[a] != null))
																							{
																								newChartData += allPossibleMetrics[a] + ",";	
																								//Alert.show("this is newChartData:"+newChartData);
																							}
																						}
																					}
																					
																					//allTempDict.Metrics += newChartData + ",";
																					allTempDict.Metrics += "," + newChartData + ",";
																					allTempDict.Metrics = allTempDict.Metrics.replace(",,", ",");
																					allTempDict.Metrics = allTempDict.Metrics.substring(0, allTempDict.Metrics.lastIndexOf(","));
																			
																					allTempFreq++;
																					allTempDict.Frequency = allTempFreq;
																					this.dbgText.text += "*{" + allTempDict.Metrics + "}";
																					
																					allChartByData[o] = allTempDict; // last All view
																					//Alert.show("handling all metrics:"+allTempDict.Metrics+" mon:"+allTempDict.Month+" dates:"+allTempDict.Dates+":key-"+allTmpKey);
																					
																					break;
																				}
																				
																			}
																		}
																	}
																	
																	o++;
																}
															}
															else
															{
																//Alert.show("in the else!!");
															}
														}
													}
												}
											}
										}
										else
										{
											
											this.dbgText.text += "this is NOT a double all";
											//Alert.show("not double All");
											//Alert.show(all_calcData[j]);
											///Alert.show("B4B4 allTmpKey for vals:" + allTmpKey + " & Dates:"+allTmpDateKey);
											
											if (!chartHasCustomDatesSubChart)
											{
												allTmpKey = tempNoMonthKey.substring(0,tempNoMonthKey.indexOf(allString)) + all_calcData[j] + "_" + tempNoMonthKey.substring(tempNoMonthKey.indexOf(allString) + allString.length);
												allTmpKey = allTmpKey.replace("|", "");
												
												
												allTmpDateKey = tempDateKey.substring(0, tempDateKey.indexOf(allString)) + all_calcData[j] + "_" + tempDateKey.substring(tempDateKey.indexOf(allString) + allString.length);
												allTmpDateKey = allTmpDateKey.replace("|", "");
											}
											else
											{
												//Alert.show("key:"+key+":Last"+key.indexOf("Last_")+":All:"+key.indexOf("All_Opr_Names"));
												if ((key.indexOf(allString)>=0)&&(key.indexOf("Last_")<0))
												{
													allTmpKey = key.substring(0,key.indexOf(allString)) + all_calcData[j] + "_" + key.substring(key.indexOf(allString) + (allString.length));
													allTmpKey = allTmpKey.substring(0, allTmpKey.indexOf(monthName)-1) + allTmpKey.substring(allTmpKey.indexOf(monthName) + monthName.length) + "_Metric_Values";
													
													allTmpDateKey = key.substring(0, key.indexOf(allString)) + all_calcData[j] + "_" + key.substring(key.indexOf(allString) + (allString.length));
													allTmpDateKey = allTmpDateKey.substring(0, allTmpDateKey.indexOf(this.chartFilter.selectedItem as String)) + "Date_Values";
													//Alert.show("first");
												}
												
												if ((key.indexOf(allString)>=0)&&(key.indexOf("Last_")>0))
												{
													allTmpKey = key.substring(0,key.indexOf(allString)) + all_calcData[j] + "_" + key.substring(key.indexOf(allString) + (allString.length));
													allTmpKey = allTmpKey.substring(0, allTmpKey.indexOf("Last")) + allTmpKey.substring(allTmpKey.indexOf("Last") + 6) + "_Metric_Values";
													
													allTmpDateKey = key.substring(0, key.indexOf(allString)) + all_calcData[j] + "_" + key.substring(key.indexOf(allString) + (allString.length));
													allTmpDateKey = allTmpDateKey.substring(0, allTmpDateKey.indexOf(this.chartFilter.selectedItem as String)) 
													allTmpDateKey = allTmpDateKey.substring(0, allTmpDateKey.indexOf("Last")) + monthName + "_" + allTmpDateKey.substring(allTmpDateKey.indexOf("Last") + 6) + "Date_Values";
													
													//Alert.show("second");
													//Alert.show("AffTr allTmpKey for vals:" + allTmpKey + " & Dates:"+allTmpDateKey+":"+all_calcData[j]+":"+allString+":"+monthName+":"+key);
												}
											}
											//Alert.show("AffTr allTmpKey for vals:" + allTmpKey + " & Dates:"+allTmpDateKey+":"+all_calcData[j]+":"+allString+":"+monthName+":"+key);
											
											// show all strng
											//this.dbgText.text += "data key all str:" + allTmpKey + "\n";
											
											if (d.getValue(allTmpKey) != null)
											{
												chartData +=  d.getValue(allTmpKey)+",";
												tempFreq++; // per months?
											}
											
											if (d.getValue(allTmpKey) != null)
											{
												 //Alert.show("value not null:"+allTmpKey+":"+allChartByData.length);
												this.dbgText.text += "data key w/ data:\n" + allTmpKey +"="+chartData;
												
												// find the matching value (shift,operator)
												//for (j = 0; j < allChartByData.length; j++ )
												
												k = 0;
												while((k<allChartByData.length))
												{
													//Alert.show("acbd:"+allChartByData.length);
													allTempDict = allChartByData[k];
													if (allTempDict != null)
													{
														allTempFreq = allTempDict.Frequency;
														
														//this.dbgText.text += "\n>dict:" + allTempDict.Chart_By + "=actual:" + all_calcData[j];
														//Alert.show("chartByA="+allTempDict.Chart_By_Alias+"-N-all Calc Data="+all_calcData[j]+"ChartBy="+allTempDict.Chart_By+":"+allTempDict.Month);
														
														if ((allTempDict.Chart_By == all_calcData[j])||(allTempDict.Chart_By_Alias == all_calcData[j]))
														{
															//this.alChart.SetCategoryAxisDates(d.getValue(allTmpDateKey));
															//this.alChart.SetCategoryAxisValues(d.getValue(allTmpKey));
															//Alert.show("chart bys work out");					
															if (!this.chartHasCustomDatesSubChart)
															{
																//this.alChart.SetCategoryAxisDates(d.getValue(allTmpDateKey));
																
																// set start end end indexes
																allTempDict.DateStartIndex = d.getValue(allTmpDateKey + "_Index");
																allTempDict.DateValueLength = d.getValue(allTmpDateKey + "_Length");
																
																//Alert.show("key:"+allTmpKey+"-Mon:"+monthName+"-DSI:"+allTempDict.DateStartIndex+"-DVL:"+allTempDict.DateValueLength);
																if (allTempDict.DateStartIndex != null)
																{
																	if ((allTempDict.DateStartIndex.length >0))
																	{
																		//Alert.show(tempDict.DateValueLength.toString());
																		validMetrics = d.getValue(allTmpKey).split(",");
																		chartData = "";
																		cntr = new Number(0);
																		dateStartIndexes = allTempDict.DateStartIndex.split(",");
																		dateValueLengths = allTempDict.DateValueLength.split(",");
																		monthMetrics = new Array();
																		for (y = 0; y < dateValueLengths.length; y++  )
																		{
																			dateStartIndex = dateStartIndexes[y];
																			dateValueLength = dateValueLengths[y];
																			//Alert.show(allTmpKey+"startIndx_:" + dateStartIndex + "&length:" + dateValueLength);
																			cntr = 0;
																			for (z = 0; z < validMetrics.length; z++ )
																			{
																				if ((z >= dateStartIndex)&&(cntr<=dateValueLength))
																				{
																					//chartData +=  validMetrics[z] + ",";
																					monthMetrics.push(validMetrics[z]);
																					//Alert.show(validMetrics[z]);
																					cntr++;
																				}
																			}
																		}
																		
																		//Alert.show(allTmpKey+":"+monthMetrics.toString()+":"+dateValueLength);
																		//Alert.show("chartBy:"+allTempDict.Chart_By_Alias+"-Month:"+monthName+"-Metrics:"+monthMetrics);
																		if (monthMetrics.length >= 1)
																		{
																			allTempDict.Metrics += "," +monthMetrics.toString();
																			allTempDict.Metrics = allTempDict.Metrics.replace(",,", ",");
																		}
																		//Alert.show("chartBy:"+allTempDict.Chart_By_Alias+"-Metrics so far:"+allTempDict.Metrics);
																		
																		
																	}
																	//Alert.show("We Have Date Index"+allTmpKey);
																}
																					
																if (allTempDict.Metrics.indexOf(",") == 0)
																{
																	allTempDict.Metrics = allTempDict.Metrics.substring(1);
																}
																
																if (allTempDict.Metrics.lastIndexOf(",") == allTempDict.Metrics.length - 1)
																{
																	allTempDict.Metrics = allTempDict.Metrics.substring(0, allTempDict.Metrics.length - 1);
																}
																
																//this.alChart.SetCategoryAxisValues(allTempDict.Metrics);
																
																allTempFreq++;
																allTempDict.Frequency = allTempFreq;
																//this.dbgText.text += "*{"+allTempDict.Metrics+"}";
																allChartByData[k] = allTempDict; // last All view
																//dataFound = true;
																break;
															}
															else
															{
																chartData  = d.getValue(allTmpDateKey);
																
																//Alert.show("non-bogus Key:"+allTmpKey);
																//Alert.show("non-bogus date Key:"+allTmpDateKey);
																
																if (chartData != null)
																{
																	// set the chart string dates first
																	if (this.chartHasCustomDatesSubChart)
																	{
																		//Alert.show("custom dates with data");
																
																		startDate = new Date();
																		startDate.setTime(Date.parse(this.chartStartDate.label));
																		
																		endDate = new Date();
																		endDate.setTime(Date.parse(this.chartEndDate.label ));
																		
																		actualDate = new Date();
																		allPossibleDates = chartData.split(',');
																		newChartData = "";
																		dateIndexes = new Array();
																		
																		for (a = 0; a < allPossibleDates.length; a++ )
																		{
																			date = allPossibleDates[a] as String;
																			if (date != null)
																			{
																				//Alert.show(date+":"+startDate.toString()+":"+endDate.toString());
																				try
																				{
																					actualDate.setTime(Date.parse(date));
																					
																					// is it in the range?
																					if ((actualDate.getTime() >= startDate.getTime()) && (actualDate.getTime() <= endDate.getTime()))
																					{
																						newChartData += allPossibleDates[a] + ",";
																						dateIndexes.push(a);
																						//Alert.show("this is a:"+a);
																					}
																				}
																				catch (err:Error)
																				{
																					Alert.show("[ERROR] problems loading custom dates:"+err.message);
																				}
																			}
																		}
																		
																		dataFound = true;
																		tempDict.Dates = newChartData;
																		tempDict.DateIndexes = dateIndexes;
																		//Alert.show("DateIndexes going in:"+tempDict.DateIndexes);
																		//this.alChart.SetCategoryAxisDates(tempDict.Dates);
																	}
																
																	
																	
																	// do the data next
																	newChartData = "";
																	allPossibleMetrics = d.getValue(allTmpKey).split(',');
																	tempDict.DateIndexes
																	
																	if (dateIndexes.length == 0 )
																	{
																		//Alert.show("There Are No Data Sets Available For Selection(4)");
																	}
																	else
																	{
																		for (a = dateIndexes[0]; a <= dateIndexes[dateIndexes.length-1]; a++ )
																		{
																			//Alert.show("this is allPossibleMetrics:" + allPossibleMetrics[a]+"and a="+a);
																			
																			if ((allPossibleMetrics[a] != null))
																			{
																				newChartData += allPossibleMetrics[a] + ",";	
																				//Alert.show("this is newChartData:"+newChartData);
																			}
																		}
																	}
																	
																	allTempDict.Metrics += newChartData + ",";
																	allTempFreq++;
																	allTempDict.Frequency = allTempFreq;
																	this.dbgText.text += "*{"+allTempDict.Metrics+"}";
																	allChartByData[k] = allTempDict; // last All view
																	//this.alChart.SetCategoryAxisDates(allTempDict.Metrics);
																	//Alert.show("handling all metrics:"+allTempDict.Metrics);
																	break;
																}
															}
														}
														else
														{
															//Alert.show("no chart by m1:"+monthName+" m2:"+allTempDict.Month);
															if(chartHasCustomDatesSubChart&&(monthName==allTempDict.Month))
															{
																chartData  = d.getValue(allTmpDateKey);
																
																//Alert.show("non-bogus Key:"+allTmpKey);
																//Alert.show("non-bogus date Key:"+allTmpDateKey);
																
																if (chartData != null)
																{
																	// set the chart string dates first
																	if (this.chartHasCustomDatesSubChart)
																	{
																		//Alert.show("custom dates with data");
																
																		startDate = new Date();
																		startDate.setTime(Date.parse(this.chartStartDate.label));
																		
																		endDate = new Date();
																		endDate.setTime(Date.parse(this.chartEndDate.label ));
																		
																		actualDate = new Date();
																		allPossibleDates = chartData.split(',');
																		newChartData = "";
																		dateIndexes = new Array();
																		
																		baseIndx = new Number(d.getValue(allTmpDateKey + "_Index"));
																		
																		for (a = 0; a < allPossibleDates.length; a++ )
																		{
																			date = allPossibleDates[a] as String;
																			if (date != null)
																			{
																				//Alert.show(date+":"+startDate.toString()+":"+endDate.toString());
																				try
																				{
																					actualDate.setTime(Date.parse(date));
																					
																					// is it in the range?
																					if ((actualDate.getTime() >= startDate.getTime()) && (actualDate.getTime() <= endDate.getTime()))
																					{
																						newChartData += allPossibleDates[a] + ",";
																						dateIndexes.push(a+baseIndx);
																						//Alert.show("this is a:"+a);
																					}
																				}
																				catch (err:Error)
																				{
																					Alert.show("[ERROR] problems loading custom dates:"+err.message);
																				}
																			}
																		}
																		
																		dataFound = true;
																		tempDict.Dates = newChartData;
																		tempDict.DateIndexes = dateIndexes;
																		//tempDict.Month = monthName;
																		//allTempDict.Dates = newChartData;
																		//allTempDict.DateIndexes = dateIndexes;
																		//allTempDict.Month = monthName;
																		//Alert.show("DateIndexes going in:"+tempDict.DateIndexes);
																		//Alert.show("Dates going in:"+tempDict.Dates+" mon:"+tempDict.Month);
																		//this.alChart.SetCategoryAxisDates(tempDict.Dates);
																	}
																
																	
																	
																	// do the data next
																	newChartData = "";
																	allPossibleMetrics = d.getValue(allTmpKey).split(',');
																	//tempDict.DateIndexes
																	
																	if (dateIndexes.length == 0 )
																	{
																		//Alert.show("There Are No Data Sets Available For Selection(4)");
																	}
																	else
																	{
																		for (a = dateIndexes[0]; a <= dateIndexes[dateIndexes.length-1]; a++ )
																		{
																			//Alert.show("this is allPossibleMetrics:" + allPossibleMetrics[a]+"and a="+a);
																			
																			if ((allPossibleMetrics[a] != null))
																			{
																				newChartData += allPossibleMetrics[a] + ",";	
																				//Alert.show("this is newChartData:"+newChartData);
																			}
																		}
																	}
																	
																	//allTempDict.Metrics += newChartData + ",";
																	allTempDict.Metrics += "," + newChartData + ",";
																	allTempDict.Metrics = allTempDict.Metrics.replace(",,", ",");
																	allTempDict.Metrics = allTempDict.Metrics.substring(0, allTempDict.Metrics.lastIndexOf(","));
																			
																	allTempFreq++;
																	allTempDict.Frequency = allTempFreq;
																	this.dbgText.text += "*{" + allTempDict.Metrics + "}";
																	allChartByData[k] = allTempDict; // last All view
																	//this.alChart.SetCategoryAxisDates(allTempDict.Metrics);
																	//Alert.show("handling all metrics:"+allTempDict.Metrics+" mon:"+allTempDict.Month+" dates:"+allTempDict.Dates);
																	break;
																}
															}
														}
													}
													else
													{
														//this.dbgText.text += "...k="+k+"isnull"
													}
													
													k++;
												}
											}
											else
											{
												//if (!dataFound)
												{
													if (!chartHasCustomDatesSubChart)
													{
														// try alias
														allTmpKey = tempNoMonthAliasKey.substring(0,tempNoMonthAliasKey.indexOf(allString)) + all_calcData[j] + "_" + tempNoMonthAliasKey.substring(tempNoMonthAliasKey.indexOf(allString) + allString.length);
														allTmpKey = allTmpKey.replace("|", "");
														
														
														allTmpDateKey = tempDateAliasKey.substring(0, tempDateAliasKey.indexOf(allString)) + all_calcData[j] + "_" + tempDateAliasKey.substring(tempDateAliasKey.indexOf(allString) + allString.length);
														allTmpDateKey = allTmpDateKey.replace("|", "");
													}
													else
													{
														//Alert.show("aliasKey:" + aliasKey + ":Last" + aliasKey.indexOf("Last_") + ":All:" + aliasKey.indexOf("All_Shifts"));
														//Alert.show("allstr:" + allString);
														
														if ((aliasKey.indexOf(allString)>=0)&&(aliasKey.indexOf("Last_")<0))
														{
															allTmpKey = aliasKey.substring(0,aliasKey.indexOf(allString)) + all_calcData[j] + "_" + aliasKey.substring(aliasKey.indexOf(allString) + (allString.length));
															allTmpKey = allTmpKey.substring(0, allTmpKey.indexOf(monthName)-1) + allTmpKey.substring(allTmpKey.indexOf(monthName) + monthName.length) + "_Metric_Values";
															
															allTmpDateKey = aliasKey.substring(0, aliasKey.indexOf(allString)) + all_calcData[j] + "_" + aliasKey.substring(aliasKey.indexOf(allString) + (allString.length));
															allTmpDateKey = allTmpDateKey.substring(0, allTmpDateKey.indexOf(this.chartFilter.selectedItem as String)) + "Date_Values";
															//Alert.show("first");
														}
														
														if ((aliasKey.indexOf(allString)>=0)&&(aliasKey.indexOf("Last_")>0))
														{
															allTmpKey = aliasKey.substring(0,aliasKey.indexOf(allString)) + all_calcData[j] + "_" + aliasKey.substring(aliasKey.indexOf(allString) + (allString.length));
															allTmpKey = allTmpKey.substring(0, allTmpKey.indexOf("Last")) + allTmpKey.substring(allTmpKey.indexOf("Last") + 6) + "_Metric_Values";
															
															allTmpDateKey = aliasKey.substring(0, aliasKey.indexOf(allString)) + all_calcData[j] + "_" + aliasKey.substring(aliasKey.indexOf(allString) + (allString.length));
															allTmpDateKey = allTmpDateKey.substring(0, allTmpDateKey.indexOf(this.chartFilter.selectedItem as String)) 
															allTmpDateKey = allTmpDateKey.substring(0, allTmpDateKey.indexOf("Last")) + monthName + "_" + allTmpDateKey.substring(allTmpDateKey.indexOf("Last") + 6) + "Date_Values";
															
															//Alert.show("second");
															//Alert.show("AffTr allTmpKey for vals:" + allTmpKey + " & Dates:"+allTmpDateKey+":"+all_calcData[j]+":"+allString+":"+monthName+":"+key);
														}
														
														
														
														//Alert.show("Alias AffTr allTmpKey for vals:" + allTmpKey + " & Dates:"+allTmpDateKey);
													}
													
													// show all strng
													//this.dbgText.text += "data key all str:" + allTmpKey + "\n";
													
													if (d.getValue(allTmpKey) != null)
													{
														chartData +=  d.getValue(allTmpKey)+",";
														tempFreq++; // per months?
													}
													
													if (d.getValue(allTmpKey) != null)
													{
														//Alert.show("got the data:"+allTmpKey);
														k = 0;
														while((k<allChartByData.length))
														{
															allTempDict = allChartByData[k];
															if (allTempDict != null)
															{
																allTempFreq = allTempDict.Frequency;
																
																this.dbgText.text += "\n>dict:" + allTempDict.Chart_By + "=actual:" + all_calcData[j];
																//Alert.show(allTempDict.Chart_By + "=actual:" + all_calcData[j]);
																if ((allTempDict.Chart_By == all_calcData[j])||(("|"+allTempDict.Chart_By) == all_calcData[j]))
																{
																	//this.alChart.SetCategoryAxisDates(d.getValue(allTmpDateKey));
																	//this.alChart.SetCategoryAxisValues(d.getValue(allTmpKey));
																				
																	if (!this.chartHasCustomDatesSubChart)
																	{
																		//this.alChart.SetCategoryAxisDates(d.getValue(allTmpDateKey));
																		
																		//Alert.show("key:"+allTmpKey+"-Mon:"+monthName+"-DSI:"+tempDict.DateStartIndex);
																		if (tempDict.DateStartIndex != null)
																		{
																			if ((tempDict.DateStartIndex.length >0))
																			{
																				//Alert.show(tempDict.DateValueLength.toString());
																				validMetrics = d.getValue(allTmpKey).split(",");
																				chartData = "";
																				cntr = new Number(0);
																				dateStartIndexes = tempDict.DateStartIndex.split(",");
																				dateValueLengths = tempDict.DateValueLength.split(",");
																				monthMetrics = new Array();
																				for (y = 0; y < dateValueLengths.length; y++  )
																				{
																					dateStartIndex = dateStartIndexes[y];
																					dateValueLength = dateValueLengths[y];
																					//Alert.show("startIndx_:" + dateStartIndex + "&length:" + dateValueLength);
																					cntr = 0;
																					for (z = 0; z < validMetrics.length; z++ )
																					{
																						if ((z >= dateStartIndex)&&(cntr<=dateValueLength))
																						{
																							//chartData +=  validMetrics[z] + ",";
																							monthMetrics.push(validMetrics[z]);
																							//Alert.show(validMetrics[z]);
																							cntr++;
																						}
																					}
																				}
																				
																				//Alert.show(allTmpKey+":"+monthMetrics.toString()+":"+dateValueLength);
																				//monthMetrics.reverse();
																				//Alert.show("Metrics so far:"+allTempDict.Metrics);
																				allTempDict.Metrics += "," +monthMetrics.toString();
																				allTempDict.Metrics = allTempDict.Metrics.replace(",,", ",");
																				
																				
																			}
																			//Alert.show("We Have Date Index"+allTmpKey);
																		}
																		else
																		{
																			allTempDict.Metrics += d.getValue(allTmpKey) + ",";
																			//Alert.show("We DON'T Have Date Index"+allTmpKey);
																		}
																							
																		if (allTempDict.Metrics.indexOf(",") == 0)
																		{
																			allTempDict.Metrics = allTempDict.Metrics.substring(1);
																		}
																		
																		if (allTempDict.Metrics.lastIndexOf(",") == allTempDict.Metrics.length - 1)
																		{
																			allTempDict.Metrics = allTempDict.Metrics.substring(0, allTempDict.Metrics.length - 1);
																		}
																		
																		//this.alChart.SetCategoryAxisValues(allTempDict.Metrics);
																		
																		allTempFreq++;
																		allTempDict.Frequency = allTempFreq;
																		//this.dbgText.text += "*{"+allTempDict.Metrics+"}";
																		allChartByData[k] = allTempDict; // last All view
																		//Alert.show("metrics:"+allTempDict.Metrics+" mon:"+allTempDict.Month+" dates:"+allTempDict.Dates);
																		dataFound = true;
																		break;
																	}
																	else
																	{
																		chartData  = d.getValue(allTmpDateKey);
																		
																		//Alert.show("non-bogus Key:"+allTmpKey);
																		//Alert.show("non-bogus date Key:"+allTmpDateKey);
																		
																		if (chartData != null)
																		{
																			// set the chart string dates first
																			if (this.chartHasCustomDatesSubChart)
																			{
																				//Alert.show("custom dates with data");
																		
																				startDate = new Date();
																				startDate.setTime(Date.parse(this.chartStartDate.label));
																				
																				endDate = new Date();
																				endDate.setTime(Date.parse(this.chartEndDate.label));
																				
																				actualDate = new Date();
																				allPossibleDates = chartData.split(',');
																				newChartData = "";
																				dateIndexes = new Array();
																				
																				for (a = 0; a < allPossibleDates.length; a++ )
																				{
																					date = allPossibleDates[a] as String;
																					if (date != null)
																					{
																						//Alert.show(date+":"+startDate.toString()+":"+endDate.toString());
																						try
																						{
																							actualDate.setTime(Date.parse(date));
																							
																							// is it in the range?
																							if ((actualDate.getTime() >= startDate.getTime()) && (actualDate.getTime() <= endDate.getTime()))
																							{
																								newChartData += allPossibleDates[a] + ",";
																								dateIndexes.push(a);
																								//Alert.show("this is a:"+a);
																							}
																						}
																						catch (err:Error)
																						{
																							Alert.show("[ERROR] problems loading custom dates:"+err.message);
																						}
																					}
																				}
																				
																				dataFound = true;
																				tempDict.Dates = newChartData;
																				tempDict.DateIndexes = dateIndexes;
																				//allTempDict.Dates = newChartData;
																				//allTempDict.DateIndexes = dateIndexes;
																				//Alert.show("DateIndexes going in:"+tempDict.DateIndexes);
																				//this.alChart.SetCategoryAxisDates(tempDict.Dates);
																			}
																		
																			//this.alChart.SetCategoryAxisDates(d.getValue(allTmpDateKey));
																			
																			// do the data next
																			newChartData = "";
																			allPossibleMetrics = d.getValue(allTmpKey).split(',');
																			//tempDict.DateIndexes
																			
																			if (dateIndexes.length == 0 )
																			{
																				//Alert.show("There Are No Data Sets Available For Selection(4)");
																			}
																			else
																			{
																				for (a = dateIndexes[0]; a <= dateIndexes[dateIndexes.length-1]; a++ )
																				{
																					//Alert.show("this is allPossibleMetrics:" + allPossibleMetrics[a]+"and a="+a);
																					
																					if ((allPossibleMetrics[a] != null))
																					{
																						newChartData += allPossibleMetrics[a] + ",";	
																						//Alert.show("this is newChartData:"+newChartData);
																					}
																				}
																			}
																			
																			allTempDict.Metrics += newChartData + ",";
																			allTempFreq++;
																			allTempDict.Frequency = allTempFreq;
																			this.dbgText.text += "*{"+allTempDict.Metrics+"}";
																			allChartByData[k] = allTempDict; // last All view
																			//this.alChart.SetCategoryAxisValues(allTempDict.Metrics);
																			//Alert.show("metrics:"+allTempDict.Metrics+" mon:"+allTempDict.Month+" dates:"+allTempDict.Dates);
																			break;
																		}
																	}
																}
																else
																{
																	//Alert.show("no chart by m1:"+monthName+" m2:"+allTempDict.Month);
																	if(chartHasCustomDatesSubChart&&(monthName==allTempDict.Month))
																	{
																		chartData  = d.getValue(allTmpDateKey);
																		
																		//Alert.show("non-bogus Key:"+allTmpKey);
																		//Alert.show("non-bogus date Key:"+allTmpDateKey+":w?"+d.getValue(allTmpDateKey+"_Index"));
																		
																		if (chartData != null)
																		{
																			// set the chart string dates first
																			if (this.chartHasCustomDatesSubChart)
																			{
																				//Alert.show("custom dates with data");
																		
																				startDate = new Date();
																				startDate.setTime(Date.parse(this.chartStartDate.label));
																				
																				endDate = new Date();
																				endDate.setTime(Date.parse(this.chartEndDate.label ));
																				
																				actualDate = new Date();
																				allPossibleDates = chartData.split(',');
																				newChartData = "";
																				dateIndexes = new Array();
																				
																				baseIndx = new Number(d.getValue(allTmpDateKey + "_Index"));
																				//allpossible and all metrics do not match here!!!
																				//Alert.show("allpossibleDates:"+allPossibleDates.join()+"-allpossiblemetrics:"+allPossibleMetrics.join());
																				for (a = 0; a < allPossibleDates.length; a++ )
																				{
																					date = allPossibleDates[a] as String;
																					if (date != null)
																					{
																						//Alert.show(date+":"+startDate.toString()+":"+endDate.toString());
																						try
																						{
																							actualDate.setTime(Date.parse(date));
																							
																							// is it in the range?
																							if ((actualDate.getTime() >= startDate.getTime()) && (actualDate.getTime() <= endDate.getTime()))
																							{
																								newChartData += allPossibleDates[a] + ",";
																								dateIndexes.push(a+baseIndx);
																								//Alert.show("this was pushed:"+(a+baseIndx)+"-this is the date:"+date);
																							}
																						}
																						catch (err:Error)
																						{
																							Alert.show("[ERROR] problems loading custom dates:"+err.message);
																						}
																					}
																				}
																				
																				dataFound = true;
																				tempDict.Dates = newChartData;
																				//Alert.show("temp indexes b4:"+dateIndexes);
																				tempDict.DateIndexes = dateIndexes;
																				//tempDict.Month = monthName;
																				//allTempDict.Dates = newChartData;
																				//allTempDict.DateIndexes = dateIndexes;
																				//allTempDict.Month = monthName;
																				//Alert.show("DateIndexes going in:"+tempDict.DateIndexes);
																				//Alert.show("Dates going in:"+tempDict.Dates+" mon:"+tempDict.Month);
																				//this.alChart.SetCategoryAxisDates(tempDict.Dates);
																			}
																		
																			
																			
																			// do the data next
																			newChartData = "";
																			allPossibleMetrics = d.getValue(allTmpKey).split(',');
																			//tempDict.DateIndexes
																			//Alert.show("all metrics:"+allPossibleMetrics.join());
																			if (dateIndexes.length == 0 )
																			{
																				//Alert.show("There Are No Data Sets Available For Selection(4)");
																			}
																			else
																			{
																				for (a = dateIndexes[0]; a <= dateIndexes[dateIndexes.length-1]; a++ )
																				{
																					//Alert.show("this is allPossibleMetrics:" + allPossibleMetrics[a]+"and a="+a+":0dateindex is="+dateIndexes[0]+" to "+dateIndexes[dateIndexes.length-1]);
																					
																					if ((allPossibleMetrics[a] != null))
																					{
																						newChartData += allPossibleMetrics[a] + ",";	
																						//Alert.show("this is newChartData:"+newChartData+":a-"+a);
																					}
																				}
																			}
																			
																			//Alert.show(newChart);
																			//allTempDict.Metrics += newChartData + ",";
																			allTempDict.Metrics += "," + newChartData + ",";
																			allTempDict.Metrics = allTempDict.Metrics.replace(",,", ",");
																			allTempDict.Metrics = allTempDict.Metrics.substring(0, allTempDict.Metrics.lastIndexOf(","));
																			allTempFreq++;
																			allTempDict.Frequency = allTempFreq;
																			this.dbgText.text += "*{" + allTempDict.Metrics + "}";
																			allChartByData[k] = allTempDict; // last All view
																			//this.alChart.SetCategoryAxisDates(allTempDict.Metrics);
																			//Alert.show("handling all metrics:"+allTempDict.Metrics+" mon:"+allTempDict.Month+" dates:"+allTempDict.Dates+":key"+allTmpKey);
																			break;
																		}
																	}
																}
															}
															else
															{
																//this.dbgText.text += "...k="+k+"isnull"
															}
															
															k++;
														}
													}
												}
											}
										}
									}
								}
							}
						}
						
						if (chartData != null)
						{
							dataFound = true;
							if (!handlingAlls)
							{
								tempDict.Metrics = chartData;
							}
							tempDict.Frequency = tempFreq;
							//tempDict.Month = monthName;
							
							//this.alChart.SetCategoryAxisValues(tempDict.Metrics);
						}
						else
						{
							//Alert.show("no data");
						}
						
						// save data back to dict
						calcData[i] = tempDict; // calender view
						//Alert.show("w data:" + tempDict.Metrics);
						//Alert.show("w mon:"+tempDict.Month);
						//Alert.show("alltemp:"+allTempDict.Metrics);
					}
					
				
					if (!handlingAlls)
					{
						if (dataFound||dateDataFound)
						{
							// set the chart string data
							this.chartMessages.text += "\n:Found data available for \n"+ key+"_Values";
						}
						else
						{
							if (calcData.length == 0)
							{
								this.chartMessages.text += "\n:There is no data available for \n" + key + "_Values";
								//Alert.show("There Are No Data Sets Available For Selection(0)");
							}
						}
				
						// save metric values and label
						this.alChart.SetAggregationData(calcData);// by month
						this.alChart.SetCategoryAxisArray(calcData);
						//Alert.show("just set the data!");
					}
					else
					{
						if (dataFound)
						{
							// set the chart string data
							this.chartMessages.text += "\n:Found data available for \n" + key + "_Values";
							//Alert.show("we got the data");
						}
						else
						{
							if (allChartByData.length == 0)
							{
								this.chartMessages.text += "\n:There is no data available for \n" + key + "_Values";
								//Alert.show("There Are No Data Sets Available For Selection(-1)");
							}
						}
				
						// save metric values and label
						this.alChart.SetAggregationData(allChartByData); // by leftmost category
						this.alChart.SetCategoryAxisArray(allChartByData);
						//Alert.show("data:"+allChartByData.length);
						
					}
				}	
			}
			/*
			if (dataFound)
			{
				Alert.show("data sukkaz");
			}
			else
			{
				Alert.show("no data");
			}
			*/
			//if (!this.chartInfoSet)
			{
				this.alChart.SetXLabel(this.xLabel);
				this.alChart.SetYLabel(this.yLabel);
				
				if (this.multipleKPIsSelected)
				{
					//Alert.show("crrntKpi:"+this.filterByComboBoxes[1].selectedItem);
					this.alChart.SetCurrentKPI_Data(this.filterByComboBoxes[1].selectedItem);
					//Alert.show("test:"+this.filterByComboBoxes[1].selectedItem);
				}
			}
		}
		
		public function SetChartInfo(i:Map ):void
		{
			//this.dbgText.text += "Set Chart Info:";
			
			var rend:int = 0;
			
			try
			{
				//this.dbgText.text += i.getValue("Renderrer") + ":";
				
				// get the renderrer
				if (i.getValue("Renderrer").indexOf("XML_SWF") >= 0)
				{
					rend = ALChart.XML_FORMAT;
				}
				
				if (i.getValue("Renderrer").indexOf("OPEN_FLASH") >= 0)
				{
					rend = ALChart.XML_FORMAT;
				}
				
				if (i.getValue("Renderrer").indexOf("AM") >= 0)
				{
					rend = ALChart.AM;
				}
				
				if (i.getValue("Renderrer").indexOf("YAHOO") >= 0)
				{
					rend = ALChart.YAHOO;
				}
				
				if (i.getValue("Renderrer").indexOf("FLEX") >= 0)
				{
					//rend = ALChart.FLEX; TODO: Hack!! Alert
					rend = ALChart.AM;
					//this.dbgText.text += "It was FLEX-"+rend+":";
				}
				
				
			}
			catch (ex:Error)
			{
				this.dbgText.text = "problems getting the chart renderrer:" + ex.message;
			}

			var display:int = 0;
			
			try
			{
				// get the display format
				if (i.getValue("DisplayFormat").indexOf("BAR") >= 0)
				{
					display = ALChart.BAR;
				}
				
				if (i.getValue("DisplayFormat").indexOf("COLUMN") >= 0)
				{
					display = ALChart.COLUMN;
				}
				
				if (i.getValue("DisplayFormat").indexOf("PIE") >= 0)
				{
					display = ALChart.PIE;
				}
				
				if (i.getValue("DisplayFormat").indexOf("AREA") >= 0)
				{
					display = ALChart.AREA;
				}
				
				if (i.getValue("DisplayFormat").indexOf("LINE") >= 0)
				{
					display = ALChart.LINE;
				}
				
				if (i.getValue("DisplayFormat").indexOf("AXIS") >= 0)
				{
					display = ALChart.AXIS;
				}
			}
			catch (ex:Error)
			{
				this.dbgText.text = "problems getting the chart display type:" + ex.message;
			}

			var type:int = 0;
			
			try
			{
				// get the display format
				if (i.getValue("DataType").indexOf("HTML") >= 0)
				{
					type = ALChart.HTML_VARIABLES;
				}
				
				if (i.getValue("DataType").indexOf("CSV_FILE") >= 0)
				{
					type = ALChart.CSV_FILE;
				}
				
				if (i.getValue("DataType").indexOf("XML_FILE") >= 0)
				{
					type = ALChart.XML_FILE;
				}
				
				if (i.getValue("DataType").indexOf("JSON") >= 0)
				{
					type = ALChart.JSON_FORMAT;
				}
				
				if (i.getValue("DataType").indexOf("DATABASE") >= 0)
				{
					type = ALChart.DATABASE;
				}
			}
			catch (ex:Error)
			{
				this.dbgText.text = "problems getting the chart data type:" + ex.message;
			}
			
			
			// create our chart object 
			switch(type)
			{
				case ALChart.HTML_VARIABLES:
					//this.dbgText.text += "It is set to HTML vars:";
					
					this.alChart = new HTMLChart(rend);
					this.alChart.DisplayFormat = display;
					this.chartInfoSet = true;
					
					//this.dbgText.text += "Rend at this point-"+this.alChart.Renderer+"-"+rend+":";
					break;
			}
			
			try
			{
				
				if (i.getValue("MetricAliasKeyIndex") != null)
				{
					this.chartMetricAliasKeyIndex = new Number(i.getValue("MetricAliasKeyIndex"));
					//Alert.show("about to see metric index:"+this.chartMetricAliasKeyIndex);
				}
			}
			catch (err:Error)
			{
				this.dbgText.text = "[ERROR]problems getting the chart metric alias key index :" + err.getStackTrace();
			}
		
			try
			{
				if (i.getValue("MetricAliasKeys") != null)
				{
					this.chartMetricAliasKeys = i.getValue("MetricAliasKeys").split(",");
					//Alert.show("about to see metric keys:"+this.chartMetricAliasKeys);
				}
			}
			catch (er:Error)
			{
				this.dbgText.text = "[ERROR]problems getting the chart metric alias keys :" + er.getStackTrace();
			}
		}
		
		private function getMetricWOMon_DataForChart(key:String, aliasKey:String,d:Map):String
		{
			var chartData:String = "";
			var currentKPIMax:Number= 0;
			//Alert.show("currentKey:"+newKey);
			
			if(key.indexOf("Availability")>=0)
			{	
				currentKPIMax = 100;
				this.alChart.SetKPIName("Availability");
			}

			if(key.indexOf("Reliability")>=0)
			{	
				currentKPIMax = 20;
				this.alChart.SetKPIName("Reliability");
			}
			/*
			if(key.indexOf("GEI_O2")>=0)
			{	
				currentKPIMax = 15;
				this.alChart.SetKPIName("GEI");
			}
			
			if(key.indexOf("UnPack_N2")>=0)
			{	
				currentKPIMax = 15;
				this.alChart.SetKPIName("N2_UnPack");
			}
			
			if(key.indexOf("Mesa_savings")>=0)
			{	
				currentKPIMax = 20;
				this.alChart.SetKPIName("VMESA");
			}
			
			if(key.indexOf("Avg_SCE")>=0)
			{	
				currentKPIMax = 20;
				this.alChart.SetKPIName("SCE");
			}
			
			if(key.indexOf("H2_Model")>=0)
			{	
				currentKPIMax = 40;
				//Alert.show("h2model");
				this.alChart.SetKPIName("H2_Model");
			}
			
			if (key.indexOf("Excess_Steam")>=0)
			{
				currentKPIMax = 20;
				this.alChart.SetKPIName("Excess_Steam");
			}
			*/
			
			//this.alChart.ResetMetricData();
			//this.alChart.SetMetricKPIMax(currentKPIMax);
			//if (!dataFound)
			{
				
				newKey = rawDatesKey + "_Metric_Values";
				newAliasKey = rawDatesAliasKey + "_Metric_Values";
				{
					// use key to get the data
					chartData = d.getValue(newKey);
					
					//this.dbgText.text = "testing!!";
					
					if ((chartData != null)&&(dateDataFound))
					{
						// set the chart string data
						if ((dateStartIndex != -1)||(dateStartIndexes.length>0))
						{
							
							var validMetrics:Array = d.getValue(newKey).split(",");
							//var dateStartIndex:Number = new Number(tempDict.DateStartIndex);
							//var dateValueLength:Number = new Number(tempDict.DateValueLength);
							chartData = "";
							var cntr:Number = new Number(0);
							monthMetrics = new Array();
							for (var y:Number = 0; y < dateValueLengths.length; y++  )
							{
								dateStartIndex = dateStartIndexes[y];
								dateValueLength = dateValueLengths[y];
								cntr = 0;
								for (var z:Number = 0; z < validMetrics.length; z++ )
								{
									if ((z >= dateStartIndex)&&(cntr<=dateValueLength))
									{
										//chartData +=  validMetrics[z] + ",";
										monthMetrics.push(validMetrics[z]);
										//Alert.show(validMetrics[z]);
										cntr++;
									}
								}
							}
							
							monthMetrics.reverse();
							chartData = monthMetrics.toString();
							//chartData = chartData.substring(0,chartData.lastIndexOf(","));
							//Alert.show("Metric data:"+chartData);
						}
						
						if (!this.chartHasCustomDatesSubChart)
						{
							this.alChart.SetMetricData(chartData);
							this.alChart.SetMetricKPIMax(currentKPIMax);
							//Alert.show("KPI going in:"+currentKPIMax);
							
						}
						else
						{
							
							newChartData = "";
							allPossibleMetrics = chartData.split(',');
							/*
							if (dateIndexes.length == 0 && (newKey.indexOf("Last_") < 0)&&(newKey.indexOf("All_") < 0)&&(!this.chartHasCustomDatesSubChart))
							{
								Alert.show("There Are No Data Sets Available For Selection(1)");
							}
							else
							*/
							{
								//Alert.show("Index Len is:"+dateIndexes.length);
								for (a = dateIndexes[0]; a <= dateIndexes[dateIndexes.length-1]; a++ )
								{
									//Alert.show("this is allPossibleMetrics:" + allPossibleMetrics[a]+"and a="+a);
									
									if (allPossibleMetrics[a] != null )
									{
										newChartData += allPossibleMetrics[a] + ",";	
										//Alert.show("this is newChartData:"+newChartData);
									}
								}
							}
							
							this.alChart.SetMetricData(newChartData);
							this.alChart.SetMetricKPIMax(currentKPIMax);
							//Alert.show("KPI going in:"+currentKPIMax);
						}
						
						this.chartMessages.text += "\n:Found data available for "+ newKey;
					}
					else
					{
						chartData = d.getValue(newAliasKey);
					
						//this.dbgText.text = "testing!!";
						
						if ((chartData != null)&&(dateDataFound))
						{
							// set the chart string data
							if ((dateStartIndex != -1)||(dateStartIndexes.length >0))
							{
								//Alert.show(dateValueLength.toString());
								validMetrics = d.getValue(newAliasKey).split(",");
								chartData = "";
								cntr = new Number(0);
								//Alert.show(validMetrics.toString());
								//Alert.show("dVL:"+dateValueLengths.toString());
								monthMetrics = new Array();
								for (y = 0; y < dateValueLengths.length; y++  )
								{
									dateStartIndex = dateStartIndexes[y];
									dateValueLength = dateValueLengths[y];
									//Alert.show("startIndx:" + dateStartIndex + "&length:" + dateValueLength);
									cntr = 0;
									for (z = 0; z < validMetrics.length; z++ )
									{
										if ((z >= dateStartIndex)&&(cntr<=dateValueLength))
										{
											//chartData +=  validMetrics[z] + ",";
											monthMetrics.push(validMetrics[z]);
											//Alert.show(validMetrics[z]);
											cntr++;
										}
									}
								}
								
								//Alert.show(monthMetrics.toString()+":"+dateValueLength);
								monthMetrics.reverse();
								//Alert.show(monthMetrics.toString());
								//chartData = chartData.substring(0,chartData.lastIndexOf(","));
								chartData = monthMetrics.toString();
								//Alert.show(newKey);
								//Alert.show(chartData);
								
							}
							
							//Alert.show(chartData);
							if (!this.chartHasCustomDatesSubChart)
							{
								this.alChart.SetMetricData(chartData);
								this.alChart.SetMetricKPIMax(currentKPIMax);
								//Alert.show("KPI going in:"+currentKPIMax);
								//Alert.show(chartData);
							}
							else
							{
								
								newChartData = "";
								allPossibleMetrics = chartData.split(',');
								//Alert.show("CHARTdATA:"+chartData);
								//Alert.show("indexes:"+dateIndexes);
								/*
								if (dateIndexes.length == 0 && (newAliasKey.indexOf("Last_") < 0)&&(newAliasKey.indexOf("All_") < 0)&&(chartData==null))
								{
									
									Alert.show("There Are No Data Sets Available For Selection(1)");
									//Alert.show("There Are No Data Sets Available For Selection(1)");
								}
								else
								*/
								{
									//Alert.show("Index Len is:"+dateIndexes.length);
									for (a = dateIndexes[0]; a <= dateIndexes[dateIndexes.length-1]; a++ )
									{
										//Alert.show("this is allPossibleMetrics:" + allPossibleMetrics[a]+"and a="+a);
										
										if (allPossibleMetrics[a] != null)
										{
											newChartData += allPossibleMetrics[a] + ",";	
											//Alert.show("this is newChartData:"+newChartData);
										}
									}
								}
								//var ay:Array = new Array(newChartData);
								//ay.reverse();
								//this.alChart.SetMetricData(ay.toString());
								this.alChart.SetMetricData(newChartData);
								this.alChart.SetMetricKPIMax(currentKPIMax);
								
								//Alert.show(newChartData);
								//Alert.show(this.chartFilters[1]);
								//Alert.show("KPI going in:"+currentKPIMax);
							}
						}
					}
				}
			}
			
			return chartData;
		}
		
		private function getMetricWMon_DataForChart(key:String, aliasKey:String,d:Map):String
		{
			var chartData:String = "";
			var currentKPIMax:Number = 0;
			
			if(key.indexOf("Availability")>=0)
			{	
				currentKPIMax = 100;
			}

			if(key.indexOf("Reliability")>=0)
			{	
				currentKPIMax = 20;
			}
			/*
			if(key.indexOf("UnPack_N2")>=0)
			{	
				currentKPIMax = 15;
			}
			
			if(key.indexOf("Mesa_savings")>=0)
			{	
				currentKPIMax = 20;
			}
			
			if(key.indexOf("Avg_SCE")>=0)
			{	
				currentKPIMax = 20;
			}
			
			if(key.indexOf("H2_Model")>=0)
			{	
				currentKPIMax = 40;
			}
			
			if (key.indexOf("Excess_Steam"))
			{
				currentKPIMax = 20;
			}
			*/
			//this.alChart.ResetMetricData();
			//this.alChart.SetMetricKPIMax(currentKPIMax);
			//if (!dataFound)
			{
				
				newKey = key + "_Metric_Values";
				newAliasKey = aliasKey + "_Metric_Values";
				{
					// use key to get the data
					chartData = d.getValue(newKey);
					
					//this.dbgText.text = "testing!!";
					
					if ((chartData != null)&&(dateDataFound))
					{
						// set the chart string data
						if (!this.chartHasCustomDatesSubChart)
						{
							this.alChart.SetMetricData(chartData);
							this.alChart.SetMetricKPIMax(currentKPIMax);
						}
						else
						{
							
							newChartData = "";
							var allPossibleMetrics:Array = chartData.split(',');
							//Alert.show("this is herre");
							
							/*
							if (dateIndexes.length == 0 && (newKey.indexOf("Last_") < 0)&&(newKey.indexOf("All_") < 0)&& )
							{
								
								Alert.show("There Are No Data Sets Available For Selection(1)");
								
								//Alert.show("There Are No Data Sets Available For Selection(1)");
							}
							else
							*/
							{
								//Alert.show("Index Len is:"+dateIndexes.length);
								for (a = dateIndexes[0]; a <= dateIndexes.length+1; a++ )
								{
									//Alert.show("this is allPossibleMetrics:" + allPossibleMetrics[a]+"and a="+a);
									
									if (allPossibleMetrics[a] != null && dateIndexes[a-dateIndexes[0]] !=null)
									{
										newChartData += allPossibleMetrics[a] + ",";	
										//Alert.show("this is newChartData:"+newChartData);
									}
								}
							}
							
							this.alChart.SetMetricData(newChartData);
							this.alChart.SetMetricKPIMax(currentKPIMax);
						}
						
						this.chartMessages.text += "\n:Found data available for "+ newKey;
					}
					else
					{
						chartData = d.getValue(newAliasKey);
					
						//this.dbgText.text = "testing!!";
						//Alert.show("in with the regular alias:"+newAliasKey);
						
						if ((chartData != null)&&(dateDataFound))
						{
							//Alert.show("Do we have data for this?");
							// set the chart string data
							if (!this.chartHasCustomDatesSubChart)
							{
								this.alChart.SetMetricData(chartData);
								this.alChart.SetMetricKPIMax(currentKPIMax);
								dataFound = true;
							}
							else
							{
								
								newChartData = "";
								allPossibleMetrics = chartData.split(',');
								
								/*
								if (dateIndexes.length == 0 && (newAliasKey.indexOf("Last_") < 0)&&(newAliasKey.indexOf("All_") < 0))
								{
									//Alert.show("we are here");
									if (d.getValue(this.setCustomDateKey(this.endDate, startDate, newAliasKey)) == null)
									{
										Alert.show("There Are No Data Sets Available For Selection(1)");
									}
									else
									{
										newAliasKey = this.setCustomDateKey(this.startDate, endDate, newAliasKey);
										chartData = d.getValue(newAliasKey);
										
										allPossibleMetrics = chartData.split(',');
										
										//Alert.show("Index Len is:"+dateIndexes.length);
										for (a = dateIndexes[0]; a <= dateIndexes.length+1; a++ )
										{
											//Alert.show("this is allPossibleMetrics:" + allPossibleMetrics[a]+"and a="+a);
											
											if (allPossibleMetrics[a] != null && dateIndexes[a-dateIndexes[0]] !=null)
											{
												newChartData += allPossibleMetrics[a] + ",";	
												//Alert.show("this is newChartData:"+newChartData);
											}
										}
									}
								}
								else
								*/
								{
									//Alert.show("Index Len is:"+dateIndexes.length);
									for (a = dateIndexes[0]; a <= dateIndexes[dateIndexes.length-1]; a++ )
									{
										//Alert.show("this is allPossibleMetrics:" + allPossibleMetrics[a]+"and a="+a);
										
										if (allPossibleMetrics[a] != null )
										{
											newChartData += allPossibleMetrics[a] + ",";	
											//Alert.show("this is newChartData:"+newChartData);
										}
									}
								}
								
								this.alChart.SetMetricData(newChartData);
								this.alChart.SetMetricKPIMax(currentKPIMax);
							}
						}
					}
				}
			}
			
			return chartData;
		}
		
		private function getDateWOKPI_DataForChart(key:String, aliasKey:String, d:Map):String
		{
			cbs = this.filterByComboBoxes[1];
			newKey = key.substring(0,key.indexOf(cbs.selectedLabel)) + "Date_Values";
			newAliasKey = aliasKey.substring(0, aliasKey.indexOf(cbs.selectedLabel)) + "Date_Values";
			
			var newKeyIndex:String = newKey + "_Index";
			var newKeyValueLength:String = newKey + "_Length";
			
			var newAliasKeyIndex:String = newAliasKey + "_Index";
			var newAliasKeyValueLength:String = newAliasKey + "_Length";
			
			var chartData:String = "";
			
			//Alert.show("newKey:"+newKey);
			{
				// use key to get the data
				chartData = d.getValue(newKey)
				dateIndexes = new Array();
				//this.dbgText.text = "testing!!";
					
				if (chartData != null)
				{
					//this.dbgText.text += chartData;
					//Alert.show("data");
					// set the chart string data
					if (!this.chartHasCustomDatesSubChart)
					{
						//this.alChart.SetDateData(chartData.substring(0, chartData.length - 1));
						
						//Alert.show("indx:len="+newKeyIndex+":"+newKeyValueLength);
						if (d.getValue(newKeyIndex) != null)
						{
							if(d.getValue(newKeyIndex).indexOf(",")>=0)
							{
								dateStartIndexes = d.getValue(newKeyIndex).split(",");
							}
							else
							{
								dateStartIndex = new Number(d.getValue(newKeyIndex));
								dateStartIndexes.push(dateStartIndex);
							}
							//Alert.show(newKeyIndex+":"+d.getValue(newKeyIndex));
						
							}
						else
						{
							//dateStartIndex = new Number(d.getValue(newAliasKeyIndex));
							if(d.getValue(newAliasKeyIndex).indexOf(",")>=0)
							{
								dateStartIndexes = d.getValue(newAliasKeyIndex).split(",");
							}
							else
							{
								dateStartIndex = new Number(d.getValue(newAliasKeyIndex));
								dateStartIndexes.push(dateStartIndex);
							}
							//Alert.show(newAliasKeyIndex+":"+d.getValue(newAliasKeyIndex));
							//Alert.show(newAliasKeyIndex+":"+dateStartIndexes.toString());
						}
						
						if (d.getValue(newKeyValueLength) != null)
						{
							//dateValueLength = new Number(d.getValue(newKeyValueLength));
							if(d.getValue(newKeyValueLength).indexOf(",")>=0)
							{
								dateValueLengths = d.getValue(newKeyValueLength).split(",");
							}
							else
							{
								dateValueLength = new Number(d.getValue(newKeyValueLength));
								dateValueLengths.push(dateValueLength);
							}
							
						}
						else
						{
							//dateValueLength = new Number(d.getValue(newAliasKeyValueLength));
							if(d.getValue(newAliasKeyValueLength).indexOf(",")>=0)
							{
								dateValueLengths = d.getValue(newAliasKeyValueLength).split(",");
							}
							else
							{
								dateValueLength = new Number(d.getValue(newAliasKeyValueLength));
								dateValueLengths.push(dateValueLength);
							}
						}
						
						//Alert.show(chartData);
						this.alChart.SetDateData(chartData.substring(0, chartData.length - 1));
						this.alChart.SetCategoryAxisDates(chartData.substring(0, chartData.length - 1));
						dateDataFound = true;
					}
					else
					{
						//Alert.show("this is custom!");
						startDate = new Date();
						startDate.setTime(Date.parse(this.chartStartDate.label));
						
						endDate = new Date();
						endDate.setTime(Date.parse(this.chartEndDate.label));
						
						actualDate = new Date();
						allPossibleDates = chartData.split(',');
						newChartData = "";
						//Alert.show("we have data");
						
						for (a = 0; a < allPossibleDates.length; a++ )
						{
							date = allPossibleDates[a] as String;
							if (date != null)
							{
								//Alert.show(actualDate.toString()+":"+startDate.toString()+":"+endDate.toString());
								try
								{
									actualDate.setTime(Date.parse(date));
									//Alert.show(actualDate.toString()+":"+startDate.toString()+":"+endDate.toString());
									// is it in the range?
									if ((actualDate.getTime() >= startDate.getTime()) && (actualDate.getTime() <= endDate.getTime()))
									{
										newChartData += allPossibleDates[a] + ",";
										dateIndexes.push(a);
										//Alert.show("this is a:"+a);
									}
								}
								catch (err:Error)
								{
									Alert.show("[ERROR] problems loading custom dates:"+err.message);
								}
							}
						}
						
						this.alChart.SetDateData(newChartData.substring(0, newChartData.length - 1));
						this.alChart.SetCategoryAxisDates(newChartData.substring(0, newChartData.length - 1));
						dateDataFound = true;
					}
					
					//this.chartMessages.text += "\n:Found data available for " + newKey;
					//Alert.show("found data");
				}
				else
				{
					chartData = d.getValue(newAliasKey)
					dateIndexes = new Array();
				
					//Alert.show(newAliasKey);
					//Alert.show(chartData);
					if (chartData != null)
					{
							//Alert.show("chart data available?");
							// set the chart string data
							
						if (d.getValue(newKeyIndex) != null)
						{
							//dateStartIndex = new Number(d.getValue(newKeyIndex));
							if(d.getValue(newKeyIndex).indexOf(",")>=0)
							{
								dateStartIndexes = d.getValue(newKeyIndex).split(",");
							}
							else
							{
								dateStartIndex = new Number(d.getValue(newKeyIndex));
								dateStartIndexes.push(dateStartIndex);
							}
							//Alert.show(newKeyIndex+":"+d.getValue(newKeyIndex));
						}
						else
						{
							//dateStartIndex = new Number(d.getValue(newAliasKeyIndex));
							if(d.getValue(newAliasKeyIndex).indexOf(",")>=0)
							{
								dateStartIndexes = d.getValue(newAliasKeyIndex).split(",");
							}
							else
							{
								dateStartIndex = new Number(d.getValue(newAliasKeyIndex));
								dateStartIndexes.push(dateStartIndex);
							}
							//Alert.show(newAliasKeyIndex+":"+d.getValue(newAliasKeyIndex));
							//Alert.show(newAliasKeyIndex+":"+dateStartIndexes.toString());
						}
						
						if (d.getValue(newKeyValueLength) != null)
						{
							//dateValueLength = new Number(d.getValue(newKeyValueLength));
							if(d.getValue(newKeyValueLength).indexOf(",")>=0)
							{
								dateValueLengths = d.getValue(newKeyValueLength).split(",");
							}
							else
							{
								dateValueLength = new Number(d.getValue(newKeyValueLength));
								dateValueLengths.push(dateValueLength);
							}
						}
						else
						{
							//Alert.show(newAliasKeyValueLength+":"+d.getValue(newAliasKeyValueLength));
							//dateValueLength = new Number(d.getValue(newAliasKeyValueLength));
							if(d.getValue(newAliasKeyValueLength).indexOf(",")>=0)
							{
								dateValueLengths = d.getValue(newAliasKeyValueLength).split(",");
							}
							else
							{
								dateValueLength = new Number(d.getValue(newAliasKeyValueLength));
								dateValueLengths.push(dateValueLength);
							}
							//Alert.show(newAliasKeyValueLength+":"+d.getValue(newAliasKeyValueLength));
							//Alert.show(newAliasKeyIndex+":"+dateValueLengths.toString());
						}
								
						if (!this.chartHasCustomDatesSubChart)
						{
								//Alert.show(chartData);
								this.alChart.SetDateData(chartData.substring(0, chartData.length - 1));
								this.alChart.SetCategoryAxisDates(chartData.substring(0, chartData.length - 1));
						
								dateDataFound = true;
						}
						else
						{
							startDate = new Date();
							startDate.setTime(Date.parse(this.chartStartDate.label));
							
							endDate = new Date();
							endDate.setTime(Date.parse(this.chartEndDate.label));
							
							actualDate = new Date();
							allPossibleDates = chartData.split(',');
							newChartData = "";
							//Alert.show("we have data");
							
							for (a = 0; a < allPossibleDates.length; a++ )
							{
								date = allPossibleDates[a] as String;
								if (date != null)
								{
									//Alert.show(actualDate.toString()+":"+startDate.toString()+":"+endDate.toString());
									try
									{
										actualDate.setTime(Date.parse(date));
										//Alert.show(actualDate.toString()+":"+startDate.toString()+":"+endDate.toString());
										// is it in the range?
										if ((actualDate.getTime() >= startDate.getTime()) && (actualDate.getTime() <= endDate.getTime()))
										{
											newChartData += allPossibleDates[a] + ",";
											dateIndexes.push(a);
											//Alert.show("this is a:"+a);
										}
									}
									catch (err:Error)
									{
										Alert.show("[ERROR] problems loading custom dates:"+err.message);
									}
								}
							}
							
							this.alChart.SetDateData(newChartData.substring(0, newChartData.length - 1));
							this.alChart.SetCategoryAxisDates(newChartData.substring(0, newChartData.length - 1));
							//this.alChart.SetCategory(tempDict.Chart_By);
							dateDataFound = true;
						}
						
						this.chartMessages.text += "\n:Found data available for " + newAliasKey;
					}
				}
			}
				
			return chartData;	
		}
		
		private function getDateWKPI_DataForChart(key:String, aliasKey:String,d:Map):String
		{
			// Variables
			newKey = key + "_Date_Values";
			newAliasKey = aliasKey + "_Date_Values";
			
			// use key to get the data
			var chartData:String = d.getValue(newKey)
					
			if (chartData != null)
			{
						
				// set the chart string data
				if (!this.chartHasCustomDatesSubChart)
				{
					this.alChart.SetDateData(chartData);
					//dateDataFound = true;
				}
				else
				{
					startDate = new Date();
					startDate.setTime(Date.parse(this.chartStartDate.label));
					
					endDate = new Date();
					endDate.setTime(Date.parse(this.chartEndDate.label));
					
					actualDate = new Date();
					allPossibleDates = chartData.split(',');
					newChartData = "";
					
					for (var a:Number = 0; a < allPossibleDates.length; a++ )
					{
						date = allPossibleDates[a] as String;
						if (date != null)
						{
							//Alert.show(actualDate.toString()+":"+startDate.toString()+":"+endDate.toString());
							try
							{
								actualDate.setTime(Date.parse(date));
								//Alert.show(actualDate.toString()+":"+startDate.toString()+":"+endDate.toString());
								// is it in the range?
								if ((actualDate.getTime() >= startDate.getTime()) && (actualDate.getTime() <= endDate.getTime()))
								{
									newChartData += allPossibleDates[a] + ",";
									dateIndexes.push(a);
									//Alert.show("this is a:"+a);
								}
							}
							catch (err:Error)
							{
								Alert.show("[ERROR] problems loading custom dates:"+err.message);
							}
						}
					}
					
					this.alChart.SetDateData(newChartData);
					//dateDataFound = true;
				}
						
				this.chartMessages.text += "\n:Found data available for " + newKey;
				//Alert.show("found data");
					
			}
			else
			{
				chartData = d.getValue(newAliasKey)
				dateIndexes = new Array();
			
				//Alert.show(newAliasKey);
				if (chartData != null)
				{
					//Alert.show("chart data available?");
					// set the chart string data
					if (!this.chartHasCustomDatesSubChart)
					{
						this.alChart.SetDateData(chartData);
						//dateDataFound = true;
					}
					else
					{
						startDate = new Date();
						startDate.setTime(Date.parse(this.chartStartDate.label));
						
						endDate = new Date();
						endDate.setTime(Date.parse(this.chartEndDate.label));
						
						actualDate = new Date();
						allPossibleDates = chartData.split(',');
						newChartData = "";
						//Alert.show("we have data");
						
						for (a = 0; a < allPossibleDates.length; a++ )
						{
							date = allPossibleDates[a] as String;
							if (date != null)
							{
								//Alert.show(actualDate.toString()+":"+startDate.toString()+":"+endDate.toString());
								try
								{
									actualDate.setTime(Date.parse(date));
									//Alert.show(actualDate.toString()+":"+startDate.toString()+":"+endDate.toString());
									// is it in the range?
									if ((actualDate.getTime() >= startDate.getTime()) && (actualDate.getTime() <= endDate.getTime()))
									{
										newChartData += allPossibleDates[a] + ",";
										dateIndexes.push(a);
										//Alert.show("this is a:"+a);
									}
								}
								catch (err:Error)
								{
									Alert.show("[ERROR] problems loading custom dates:"+err.message);
								}
							}
						}
						
						this.alChart.SetDateData(newChartData);
						//dateDataFound = true;
					}
				}
			}
		
			return chartData;
		}
		
		private function getPipedDataForChart(key:String, aliasKey:String, d:Map):String
		{
			newKey = key + "_Values";
			newAliasKey = aliasKey + "_Values";
			
			// use basic key to get the data
			var chartData:String = d.getValue(newKey)
			
			// if that doesn;t work try the alias
			if (chartData == null)
			{
				chartData = d.getValue(newAliasKey)
			}
			
			return chartData;
		}
		
		private function setRawDateAndAliasKeys(key:String, aliasKey:String):void
		{
			///if (chartHasCustomDatesSubChart != true )
			{
				if (key.indexOf("January") >= 0)
				{
					if (chartHasCustomDatesSubChart != true )
					{
						chartStartDate.label = "1/01/" + new String(chartEndChooser.displayedYear);
						this.chartStartChooser.selectedDate = new Date(chartEndChooser.displayedYear,0,1);
						lastDayInMonth = new Date(chartStartChooser.displayedYear,1,0).getDate() as Number;
						chartEndDate.label = "1/"+lastDayInMonth+"/" + new String(chartEndChooser.displayedYear);
						this.chartEndChooser.selectedDate = new Date(chartEndChooser.displayedYear, 0, lastDayInMonth);
					}
					
					rawDatesKey = key.substring(0, key.indexOf("_January")) + key.substring(key.indexOf("_January") + 8); 
					rawDatesAliasKey = aliasKey.substring(0, aliasKey.indexOf("_January")) + aliasKey.substring(aliasKey.indexOf("_January") + 8); 
				}
				
				if (key.indexOf("February") >= 0)
				{
					if (chartHasCustomDatesSubChart != true )
					{
						chartStartDate.label = "2/01/" + new String(chartEndChooser.displayedYear);
						this.chartStartChooser.selectedDate = new Date(chartEndChooser.displayedYear,1,1);
						lastDayInMonth = new Date(chartStartChooser.displayedYear,2,0).getDate() as Number;
						chartEndDate.label = "2/" + lastDayInMonth + "/" + new String(chartEndChooser.displayedYear);
						this.chartEndChooser.selectedDate = new Date(chartEndChooser.displayedYear, 1, lastDayInMonth);
					}
					
					rawDatesKey = key.substring(0, key.indexOf("_February")) + key.substring(key.indexOf("_February") + 9); 
					rawDatesAliasKey = aliasKey.substring(0, aliasKey.indexOf("_February")) + aliasKey.substring(aliasKey.indexOf("_February") + 9); 
				
				}
				
				if (key.indexOf("March") >= 0)
				{
					if (chartHasCustomDatesSubChart != true )
					{
						chartStartDate.label = "3/01/" + new String(chartEndChooser.displayedYear);
						lastDayInMonth = new Date(chartStartChooser.displayedYear,3,0).getDate() as Number;
						chartEndDate.label = "3/" + lastDayInMonth + "/" + new String(chartEndChooser.displayedYear);
						this.chartStartChooser.selectedDate = new Date(chartEndChooser.displayedYear,2,1);
						this.chartEndChooser.selectedDate = new Date(chartEndChooser.displayedYear, 2, lastDayInMonth);
					}
					
					rawDatesKey = key.substring(0, key.indexOf("_March")) + key.substring(key.indexOf("_March") + 6); 
					rawDatesAliasKey = aliasKey.substring(0, aliasKey.indexOf("_March")) + aliasKey.substring(aliasKey.indexOf("_March") + 6); 
				
				}
				
				if (key.indexOf("April") >= 0)
				{
					if (chartHasCustomDatesSubChart != true )
					{
						chartStartDate.label = "4/01/" + new String(chartEndChooser.displayedYear);
						lastDayInMonth = new Date(chartStartChooser.displayedYear,4,0).getDate() as Number;
						chartEndDate.label = "4/"+lastDayInMonth+"/" + new String(chartEndChooser.displayedYear);
						this.chartStartChooser.selectedDate = new Date(chartEndChooser.displayedYear,3,1);
						this.chartEndChooser.selectedDate = new Date(chartEndChooser.displayedYear, 3, lastDayInMonth);
					}
					
					rawDatesKey = key.substring(0, key.indexOf("_April")) + key.substring(key.indexOf("_April") + 6); 
					rawDatesAliasKey = aliasKey.substring(0, aliasKey.indexOf("_April")) + aliasKey.substring(aliasKey.indexOf("_April") + 6); 
				
				}
				
				if (key.indexOf("May") >= 0)
				{
					if (chartHasCustomDatesSubChart != true )
					{
						chartStartDate.label = "5/01/" + new String(chartEndChooser.displayedYear);
						lastDayInMonth = new Date(chartStartChooser.displayedYear,5,0).getDate() as Number;
						chartEndDate.label = "5/"+lastDayInMonth+"/" + new String(chartEndChooser.displayedYear);
						this.chartStartChooser.selectedDate = new Date(chartEndChooser.displayedYear,4,1);
						this.chartEndChooser.selectedDate = new Date(chartEndChooser.displayedYear, 4, lastDayInMonth);
					}
					
					rawDatesKey = key.substring(0, key.indexOf("_May")) + key.substring(key.indexOf("_May") + 4); 
					rawDatesAliasKey = aliasKey.substring(0, aliasKey.indexOf("_May")) + aliasKey.substring(aliasKey.indexOf("_May") + 4); 
				
				}
				
				if (key.indexOf("June") >= 0)
				{
					if (chartHasCustomDatesSubChart != true )
					{
						chartStartDate.label = "6/01/" + new String(chartEndChooser.displayedYear);
						lastDayInMonth = new Date(chartStartChooser.displayedYear,6,0).getDate() as Number;
						chartEndDate.label = "6/"+lastDayInMonth+"/" + new String(chartEndChooser.displayedYear);
						this.chartStartChooser.selectedDate = new Date(chartEndChooser.displayedYear,5,1);
						this.chartEndChooser.selectedDate = new Date(chartEndChooser.displayedYear, 5, lastDayInMonth);
					}
					
					rawDatesKey = key.substring(0, key.indexOf("_June")) + key.substring(key.indexOf("_June") + 5); 
					rawDatesAliasKey = aliasKey.substring(0, aliasKey.indexOf("_June")) + aliasKey.substring(aliasKey.indexOf("_June") + 5); 
				
					
				}
				
				if (key.indexOf("July") >= 0)
				{
					if (chartHasCustomDatesSubChart != true )
					{
						chartStartDate.label = "7/01/" + new String(chartEndChooser.displayedYear);
						lastDayInMonth = new Date(chartStartChooser.displayedYear,7,0).getDate() as Number;
						chartEndDate.label = "7/"+lastDayInMonth+"/" + new String(chartEndChooser.displayedYear);
						this.chartStartChooser.selectedDate = new Date(chartEndChooser.displayedYear,6,1);
						this.chartEndChooser.selectedDate = new Date(chartEndChooser.displayedYear, 6, lastDayInMonth);
					}
					
					rawDatesKey = key.substring(0, key.indexOf("_July")) + key.substring(key.indexOf("_July") + 5); 
					rawDatesAliasKey = aliasKey.substring(0, aliasKey.indexOf("_July")) + aliasKey.substring(aliasKey.indexOf("_July") + 5); 
				}
				
				if (key.indexOf("August") >= 0)
				{
					if (chartHasCustomDatesSubChart != true )
					{
						chartStartDate.label = "8/01/" + new String(chartEndChooser.displayedYear);
						lastDayInMonth = new Date(chartStartChooser.displayedYear,8,0).getDate() as Number;
						chartEndDate.label = "8/"+lastDayInMonth+"/" + new String(chartEndChooser.displayedYear);
						this.chartStartChooser.selectedDate = new Date(chartEndChooser.displayedYear,7,1);
						this.chartEndChooser.selectedDate = new Date(chartEndChooser.displayedYear, 7, lastDayInMonth);
					}
				
					rawDatesKey = key.substring(0, key.indexOf("_August")) + key.substring(key.indexOf("_August") + 7); 
					rawDatesAliasKey = aliasKey.substring(0, aliasKey.indexOf("_August")) + aliasKey.substring(aliasKey.indexOf("_August") + 7); 
					
				}
				
				if (key.indexOf("September") >= 0)
				{
					if (chartHasCustomDatesSubChart != true )
					{
						chartStartDate.label = "09/01/" + new String(chartEndChooser.displayedYear);
						lastDayInMonth = new Date(chartStartChooser.displayedYear,9,0).getDate() as Number;
						chartEndDate.label = "9/"+lastDayInMonth+"/" + new String(chartEndChooser.displayedYear);
						this.chartStartChooser.selectedDate = new Date(chartEndChooser.displayedYear,8,1);
						this.chartEndChooser.selectedDate = new Date(chartEndChooser.displayedYear, 8, lastDayInMonth);
					}
					
					rawDatesKey = key.substring(0, key.indexOf("_September")) + key.substring(key.indexOf("_September") + 10 );
					rawDatesAliasKey = aliasKey.substring(0, aliasKey.indexOf("_September")) + aliasKey.substring(aliasKey.indexOf("_September") + 10); 
					
					
				}
				
				if (key.indexOf("October") >= 0)
				{
					if (chartHasCustomDatesSubChart != true )
					{
						chartStartDate.label = "10/01/" + new String(chartEndChooser.displayedYear);
						lastDayInMonth = new Date(chartStartChooser.displayedYear,10,0).getDate() as Number;
						chartEndDate.label = "10/"+lastDayInMonth+"/" + new String(chartEndChooser.displayedYear);
						this.chartStartChooser.selectedDate = new Date(chartEndChooser.displayedYear,9,1);
						this.chartEndChooser.selectedDate = new Date(chartEndChooser.displayedYear, 9, lastDayInMonth);
					}
					
					rawDatesKey = key.substring(0, key.indexOf("_October")) + key.substring(key.indexOf("_October") + 8 );
					rawDatesAliasKey = aliasKey.substring(0, aliasKey.indexOf("_October")) + aliasKey.substring(aliasKey.indexOf("_October") + 8); 
					
				}
				
				if (key.indexOf("November") >= 0)
				{
					if (chartHasCustomDatesSubChart != true )
					{
						chartStartDate.label = "11/01/" + new String(chartEndChooser.displayedYear);
						lastDayInMonth = new Date(chartStartChooser.displayedYear,11,0).getDate() as Number;
						chartEndDate.label = "11/"+lastDayInMonth+"/" + new String(chartEndChooser.displayedYear);
						this.chartStartChooser.selectedDate = new Date(chartEndChooser.displayedYear,10,1);
						this.chartEndChooser.selectedDate = new Date(chartEndChooser.displayedYear, 10, lastDayInMonth);
					}
					
					rawDatesKey = key.substring(0, key.indexOf("_November")) + key.substring(key.indexOf("_November") + 9 );
					rawDatesAliasKey = aliasKey.substring(0, aliasKey.indexOf("_November")) + aliasKey.substring(aliasKey.indexOf("_November") + 9); 
					
				}
				
				if (key.indexOf("December") >= 0)
				{
					if (chartHasCustomDatesSubChart != true )
					{
						chartStartDate.label = "12/01/" + new String(chartEndChooser.displayedYear);
						lastDayInMonth = new Date(chartStartChooser.displayedYear,12,0).getDate() as Number;
						chartEndDate.label = "12/"+lastDayInMonth+"/" + new String(chartEndChooser.displayedYear);
						this.chartStartChooser.selectedDate = new Date(chartEndChooser.displayedYear,11,1);
						this.chartEndChooser.selectedDate = new Date(chartEndChooser.displayedYear, 11, lastDayInMonth);
					}
					
					rawDatesKey = key.substring(0, key.indexOf("_December")) + key.substring(key.indexOf("_December") + 9 );
					rawDatesAliasKey = aliasKey.substring(0, aliasKey.indexOf("_December")) + aliasKey.substring(aliasKey.indexOf("_December") + 9); 
					
				}
			}
		}
		
		private function setCustomDateKey(boundaryDate:Date, currentDate:Date, key:String):String
		{
			var newKey:String = "";
			
			if((boundaryDate.month == currentDate.month)&&(boundaryDate.month == 0)&&(key.indexOf("January")<0))
			{
				newKey = key.substr(0, key.indexOf(this.chartByLabels[2].text));
				newKey+= "January" + key.substr(key.indexOf(this.chartByLabels[2].text)+ this.chartByLabels[2].text.length);
			}
			
			if((boundaryDate.month == currentDate.month)&&(boundaryDate.month == 1)&&(key.indexOf("February")<0))
			{
				newKey = key.substr(0, key.indexOf(this.chartByLabels[2].text));
				newKey+= "February" + key.substr(key.indexOf(this.chartByLabels[2].text)+ this.chartByLabels[2].text.length);
			}
			
			if((boundaryDate.month == currentDate.month)&&(boundaryDate.month == 2)&&(key.indexOf("March")<0))
			{
				newKey = key.substr(0, key.indexOf(this.chartByLabels[2].text));
				newKey+= "March" + key.substr(key.indexOf(this.chartByLabels[2].text)+ this.chartByLabels[2].text.length);
			
			}
			
			if((boundaryDate.month == currentDate.month)&&(boundaryDate.month == 3)&&(key.indexOf("April")<0))
			{
				newKey = key.substr(0, key.indexOf(this.chartByLabels[2].text));
				newKey+= "April" + key.substr(key.indexOf(this.chartByLabels[2].text)+ this.chartByLabels[2].text.length);
			}
			
			if((boundaryDate.month == currentDate.month)&&(boundaryDate.month == 4)&&(key.indexOf("May")<0))
			{
				newKey = key.substr(0, key.indexOf(this.chartByLabels[2].text));
				newKey+= "May" + key.substr(key.indexOf(this.chartByLabels[2].text)+ this.chartByLabels[2].text.length);
			}
			
			if((boundaryDate.month == currentDate.month)&&(boundaryDate.month == 5)&&(key.indexOf("June")<0))
			{
				newKey = key.substr(0, key.indexOf(this.chartByLabels[2].text));
				newKey+= "June" + key.substr(key.indexOf(this.chartByLabels[2].text)+ this.chartByLabels[2].text.length);
			}
			
			if((boundaryDate.month == currentDate.month)&&(boundaryDate.month == 6)&&(key.indexOf("July")<0))
			{
				newKey = key.substr(0, key.indexOf(this.chartByLabels[2].text));
				newKey+= "July" + key.substr(key.indexOf(this.chartByLabels[2].text)+ this.chartByLabels[2].text.length);	
			}
			
			if((boundaryDate.month == currentDate.month)&&(boundaryDate.month == 7)&&(key.indexOf("August")<0))
			{
				newKey = key.substr(0, key.indexOf(this.chartByLabels[2].text));
				newKey+= "August" + key.substr(key.indexOf(this.chartByLabels[2].text)+ this.chartByLabels[2].text.length);
			}
			
			if((boundaryDate.month == currentDate.month)&&(boundaryDate.month == 8)&&(key.indexOf("September")<0))
			{
				newKey = key.substr(0, key.indexOf(this.chartByLabels[2].text));
				newKey+= "September" + key.substr(key.indexOf(this.chartByLabels[2].text)+ this.chartByLabels[2].text.length);
			}
			
			if((boundaryDate.month == currentDate.month)&&(boundaryDate.month == 9)&&(key.indexOf("October")<0))
			{
				newKey = key.substr(0, key.indexOf(this.chartByLabels[2].text));
				newKey+= "October" + key.substr(key.indexOf(this.chartByLabels[2].text)+ this.chartByLabels[2].text.length);
			}
			
			if((boundaryDate.month == currentDate.month)&&(boundaryDate.month == 10)&&(key.indexOf("November")<0))
			{
				newKey = key.substr(0, key.indexOf(this.chartByLabels[2].text));
				newKey+= "November" + key.substr(key.indexOf(this.chartByLabels[2].text)+ this.chartByLabels[2].text.length);
			}
			
			if((boundaryDate.month == currentDate.month)&&(boundaryDate.month == 11)&&(key.indexOf("December")<0))
			{
				newKey = key.substr(0, key.indexOf(this.chartByLabels[2].text));
				newKey+= "December" + key.substr(key.indexOf(this.chartByLabels[2].text)+ this.chartByLabels[2].text.length);
			}
			if (newKey.length == 0)
			{
				newKey = key;
			}
			
			return newKey;
		}
		
		/* More Private Functions*/
		private function init(e:Event = null):void 
		{
			removeEventListener(Event.ADDED_TO_STAGE, init);
			// entry point
			
			this.dbgText = new Label();
			
			dbgText.x = 400;
			dbgText.y = 200;
			dbgText.width = 1000;
			dbgText.height = 100;
			
			//this.dbgText.text = "this is a test:";
			
			//this.addChild(dbgText);
			
			//this.addChild(this.lblVersion);
			
			if(!chartInfoSet)
			{
				filtersRedrawn = false;
				
				// call the methods yourself
				//this.dbgText.text += "about to call methods";
				
				// set the info map
				this.SetChartInfo(this.appLoader.GetChartValues());
				
				// set the arrays
				this.SetChartCategories(this.appLoader.GetCategoryValues());
				
				// create the chart
				this.CreateChart();
			
				this.multipleKPIsSelected = true;
				
				// set the data map
				///this.SetChartData(this.appLoader.GetChartData());
				var a:Number = this.appLoader.GetChartDataKeys().indexOf("KPI_Type_Values");
				var kpiFilters:Array = this.appLoader.GetChartDataKeyValues()[a].split(',');
				var cb:ComboBox = this.chartFilter as ComboBox;
				var kpi:String = "";
				var k:Number = 0;
				
				// Hot Desk First
				for (k = 0; k < kpiFilters.length; k++ )
				{
					kpi = kpiFilters[k];
					//l.text = kpi;
					
					cb.selectedItem = kpiFilters[k];
					filterByComboBoxes[0].selectedItem = "CoGenPlant"
					
					// set the data map
					this.SetChartData(this.appLoader.GetChartData());
					
					//Alert.show("label:"+l);
					//Alert.show(cbb.selectedItems[k]);
				}
				
				// Cold Desk Second
				for (k = 0; k < kpiFilters.length; k++ )
				{
					kpi = kpiFilters[k];
					//l.text = kpi;
					
					cb.selectedItem = kpiFilters[k];
					filterByComboBoxes[0].selectedItem = "ASUPlant"
					
					// set the data map
					this.SetChartData(this.appLoader.GetChartData());
					
					//Alert.show("label:"+l);
					//Alert.show(cbb.selectedItems[k]);
				}
				
				this.cbs.selectedItem = this.chartFilters[0];
				filterByComboBoxes[0].selectedItem = "All_Plants"
					
				
				// finally draw the chart with the data
				this.DrawChart();
			
				//this.dbgText.text = this.dbgText.text + "-methods called!";
				//this.dbgText.text += this.appLoader.GetDebugTxt();
				
				
				
			}
			
			// messages Label
			//this.addChild(this.chartMessagesLabel);
			
			// actual Messages
			//this.addChild(this.chartMessages);
		}
		
		private function reDrawChart(e:DropdownEvent = null):void 
		{
			var cb:ComboBox = e.target as ComboBox;
			var desk:String = "";
			var dataValues:Array = this.appLoader.GetChartDataKeyValues()[4].split(",");
			var dfcbb:MultiSelectDropDownCtl =  filterByComboBoxes[0] as MultiSelectDropDownCtl;
			var cbb:MultiSelectDropDownCtl = e.target as MultiSelectDropDownCtl;
			var a:Number = this.appLoader.GetChartDataKeys().indexOf("KPI_Type_Values");
			var kpiFilters:Array = this.appLoader.GetChartDataKeyValues()[a].split(',');
			var cF:Array = new Array();
			var cbc:MultiSelectDropDownCtl;
			var k:Number;
			
			if ((e.type == Event.OPEN)||(e.type == Event.CLOSE))
			{
				//Alert.show(cb.selectedLabel);
			
				//HACK!!
				if (cb.selectedLabel == "ASUPlant")
				{
					desk = "ASU";
					cbc = this.chartHeader.getChildAt(9) as MultiSelectDropDownCtl;
					cF = new Array();
					cbc.dropdown.dataProvider = cF;
					cbc.dataProvider = cF;
					cF.push("All_Kpi");
					for (k = 0; k < dataValues.length; k++ )
					{
						cF.push(dataValues[k]);
					}
					
					cbc.dataProvider = cF;
					cbc.selectedIndex = 0;
				}
				
				if (cb.selectedLabel == "CoGenPlant")
				{
					
					desk = "CoGen";
					cbc = this.chartHeader.getChildAt(9) as MultiSelectDropDownCtl;
					cF = new Array();
					cbc.dropdown.dataProvider = cF;
					cbc.dataProvider = cF;
					cF.push("All_Kpi");
					for (k = 0; k < dataValues.length; k++ )
					{
						cF.push(dataValues[k]);
					}
					
					cbc.dataProvider = cF;
					cbc.selectedIndex = 0;
				}
				
				if (cb.selectedLabel == "All_Plants")
				{
					
					desk = "All";
					cbc = this.chartHeader.getChildAt(9) as MultiSelectDropDownCtl;
					cF = new Array();
					cbc.dropdown.dataProvider = cF;
					cbc.dataProvider = cF;
					cF.push("All_Kpi");
					for (k = 0; k < dataValues.length; k++ )
					{
						cF.push(dataValues[k]);
					}
					
					cbc.dataProvider = cF;
					cbc.selectedIndex = 0;
				}
			}
			
			if (e.type == Event.CLOSE)
			{
				this.alChart.ResetChartData();
				this.multipleKPIsSelected = false;
			
				if (cbb == this.filterByComboBoxes[1])
				{
					this.alChart.ResetMetricData();
					
					this.setComboFilterBy(e);
					
				}
				else
				{
					this.chartMessages.text = "Refreshing Chart:Dropdown clicked";
					
					if (this.chartFilter.selectedLabel == "All_Kpi")
					{
						this.multipleKPIsSelected  = true;
					
						if (dfcbb.selectedItem != "All_Plants")
						{
							for (k = 0; k < this.chartFilters.length; k++ )
							{
								var kpi:String = this.chartFilters[k];
								//l.text = kpi;
								//var cbk:ComboBox = this.filterByComboBoxes[1];
								var cbk:ComboBox = this.chartFilter as ComboBox;
								cbk.selectedIndex = k;
								
								//if (k < 5)
								{
									// set the data map
									this.SetChartData(this.appLoader.GetChartData());
								}
								//Alert.show("label:"+l);
								//Alert.show(kpi);
							}
							
							//this.chartFilter.selectedIndex = 0;
							//this.SetChartData(this.appLoader.GetChartData());
							cbk.selectedIndex = 0;
						}
						else
						{
							// Hot Desk First
								for (k = 0; k < kpiFilters.length; k++ )
								{
									kpi = kpiFilters[k];
									//l.text = kpi;
									
									cbk = this.chartFilter as ComboBox;
									cbk.selectedIndex = k+1;
									filterByComboBoxes[0].selectedItem = "CoGenPlant";
									
									// set the data map
									this.SetChartData(this.appLoader.GetChartData());
									
									//Alert.show("label:"+l);
									//Alert.show(cbb.selectedItems[k]);
								}
								
								// Cold Desk Second
								for (k = 0; k < kpiFilters.length; k++ )
								{
									kpi = kpiFilters[k];
									//l.text = kpi;
									
									cbk = this.chartFilter as ComboBox;
									cbk.selectedIndex = k+1;
									filterByComboBoxes[0].selectedItem = "ASUPlant";
									
									// set the data map
									this.SetChartData(this.appLoader.GetChartData());
									
									//Alert.show("label:"+l);
									//Alert.show(cbb.selectedItems[k]);
								}
								
								//this.cbs.selectedItem = this.chartFilters[0];
								cbk.selectedIndex = 0;
								filterByComboBoxes[0].selectedItem = "All_Plants";
						}
						
						//Alert.show("b4 draw chart");
						this.DrawChart();
						//Alert.show("aftr");
						
					}
					else
					{
						// set the data map
						this.SetChartData(this.appLoader.GetChartData());
							
						// finally draw the chart with the data
						this.DrawChart();
					}
				}
			}
		}
		
		private function setChartBy(e:MouseEvent = null):void 
		{
			//Alert.show("test");
			
			var l:Label = this.chartByLabels[e.currentTarget.parent.id] as Label;
			var lb:LinkButton = e.currentTarget as LinkButton;
			
			this.chartHasCustomDatesSubChart = false;
			
			this.chartMessages.text = "Changing Chart By Values:From-"+l.text+" To-"+lb.label;
			
			l.text = lb.label;
			
			//Alert.show("selected chartBy:"+l.text);
			
			// set the data map
			this.SetChartData(this.appLoader.GetChartData());
				
			// finally draw the chart with the data
			this.DrawChart();
			
			
		}
		
		private function setChartByCustomDates(e:MouseEvent = null):void 
		{
			var lb:LinkButton = e.currentTarget as LinkButton;
			this.alChart.ResetChartData();
			this.chartMessages.text = "Changing Chart By Values: To-"+lb.label;
			
			chartHasCustomDatesSubChart = true;
			
			this.hideStartDateChooser(null);
			this.hideEndDateChooser(null);
			
			
			try
			{
				var d1:Date = new Date();
				d1.setTime(Date.parse(this.chartStartDate.label));
				
				var d2:Date = new Date();
				d2.setTime(Date.parse(this.chartEndDate.label));
				
				var bd:Date = new Date(d1.fullYear + 1, d1.month, d1.date);
				
				if (d2.getTime() <= bd.getTime())
				{
					var dfcbb:MultiSelectDropDownCtl =  filterByComboBoxes[0] as MultiSelectDropDownCtl;
					var fcbb:MultiSelectDropDownCtl =  filterByComboBoxes[1] as MultiSelectDropDownCtl;
					var cb:ComboBox = filterByComboBoxes[1] as ComboBox;
					var a:Number = this.appLoader.GetChartDataKeys().indexOf("KPI_Type_Values");
					var kpiFilters:Array = this.appLoader.GetChartDataKeyValues()[a].split(',');
					
					//Alert.show("all KPI!!"+this.multipleKPIsSelected);	
						
					if (this.multipleKPIsSelected)
					{
						if (fcbb.selectedItems[0] == "All_Kpi")
						{
							if (dfcbb.selectedItem != "All_Plants")
							{
								for (k = 1; k < this.chartFilters.length; k++ )
								{
									//kpi = this.chartFilters[k];
									//l.text = kpi;
									cb.selectedIndex = k;
									
									if (k < 5)
									{
										// set the data map
										this.SetChartData(this.appLoader.GetChartData());
									}
									
									//Alert.show("label:"+this.chartFilters[k]);
									//Alert.show(kpi);
								}
							
								cb.selectedIndex = 0;
							}
							else
							{
								// Hot Desk First
								for (k = 0; k < kpiFilters.length-4; k++ )
								{
									kpi = kpiFilters[k];
									//l.text = kpi;
									
									cb.selectedItem = kpiFilters[k];
									filterByComboBoxes[0].selectedItem = "CoGenPlant";
									
									// set the data map
									this.SetChartData(this.appLoader.GetChartData());
									
									//Alert.show("label:"+l);
									//Alert.show(cbb.selectedItems[k]);
								}
								
								// Cold Desk Second
								for (k = 4; k < kpiFilters.length; k++ )
								{
									kpi = kpiFilters[k];
									//l.text = kpi;
									
									cb.selectedItem = kpiFilters[k];
									filterByComboBoxes[0].selectedItem = "ASUPlant";
									
									// set the data map
									this.SetChartData(this.appLoader.GetChartData());
									
									//Alert.show("label:"+l);
									//Alert.show(cbb.selectedItems[k]);
								}
								
								//this.cbs.selectedItem = this.chartFilters[0];
								cb.selectedIndex = 0;
								filterByComboBoxes[0].selectedItem = "All_Plants";
								
							}
							
						}
						else
						{
							if (dfcbb.selectedItem == "All_Plants" )
							{
								// Cold Desk First
								for (k = 0; k < fcbb.selectedItems.length; k++ )
								{
									//kpi = kpiFilters[k];
									//l.text = kpi;
									
									//cb.selectedIndex = k+1;
									cb.selectedItem = fcbb.selectedItems[k];
									filterByComboBoxes[0].selectedItem = "CoGenPlant";
									
									if (fcbb.selectedIndices[k] < 5)
									{
										// set the data map
										this.SetChartData(this.appLoader.GetChartData());
									}
									//Alert.show("label-k:"+cbb.selectedIndices[k]+" of "+cbb.selectedItems.length);
									//Alert.show(cbb.selectedItems[k]);
								}
							
								// Hot Desk Second
								for (k = 0; k < fcbb.selectedItems.length; k++ )
								{
									//kpi = kpiFilters[k];
									//l.text = kpi;
									
									//cb.selectedIndex = k+1;
									cb.selectedItem = fcbb.selectedItems[k];
									filterByComboBoxes[0].selectedItem = "ASUPlant";
									
									if (fcbb.selectedIndices[k] > 4)
									{
										// set the data map
										this.SetChartData(this.appLoader.GetChartData());
									}
									//Alert.show("label-k:"+cbb.selectedIndices[k]+" in "+cbb.selectedItems.length);
									//Alert.show(cbb.selectedItems[k]);
								}
							
								//cb.selectedItem = this.chartFilters[0];
								filterByComboBoxes[0].selectedItem = "All_Plants";
							}
							else
							{
								for (var k:Number = 0; k < fcbb.selectedItems.length; k++ )
								{
									var kpi:String = fcbb.selectedItems[k];
									//l.text = kpi;
									cb.selectedItem = fcbb.selectedItems[k];
									
									// set the data map
									this.SetChartData(this.appLoader.GetChartData());
									
									//Alert.show("label:"+l);
									//Alert.show(cbb.selectedItems[k]);
									//Alert.show("KPI"+cb.selectedItem)
								}
							}
						}
					}
					else
					{
						if (dfcbb.selectedItem == "All_Plants" )
						{
							// Cold Desk First
								for (k = 0; k < fcbb.selectedItems.length; k++ )
								{
									//kpi = kpiFilters[k];
									//l.text = kpi;
									
									//cb.selectedIndex = k+1;
									cb.selectedItem = fcbb.selectedItems[k];
									filterByComboBoxes[0].selectedItem = "CoGenPlant";
									
									if (fcbb.selectedIndices[k] < 5)
									{
										// set the data map
										this.SetChartData(this.appLoader.GetChartData());
									}
									//Alert.show("label-k:"+cbb.selectedIndices[k]+" of "+cbb.selectedItems.length);
									//Alert.show(cbb.selectedItems[k]);
								}
							
								// Hot Desk Second
								for (k = 0; k < fcbb.selectedItems.length; k++ )
								{
									//kpi = kpiFilters[k];
									//l.text = kpi;
									
									//cb.selectedIndex = k+1;
									cb.selectedItem = fcbb.selectedItems[k];
									filterByComboBoxes[0].selectedItem = "ASUPlant";
									
									if (fcbb.selectedIndices[k] > 4)
									{
										// set the data map
										this.SetChartData(this.appLoader.GetChartData());
									}
									//Alert.show("label-k:"+cbb.selectedIndices[k]+" in "+cbb.selectedItems.length);
									//Alert.show(cbb.selectedItems[k]);
								}
							
								//cb.selectedItem = this.chartFilters[0];
								filterByComboBoxes[0].selectedItem = "All_Plants";
						}
						else
						{
							// set the data map
							this.SetChartData(this.appLoader.GetChartData());
						}
					}	
					
					// finally draw the chart with the data
					this.DrawChart();
					
					//this.dbgText.text = "";
					chartHasCustomDatesSubChart = false;
				}
				else
				{
					Alert.show("Only a year at a time can be charted in the viewer!(-6)");
				}
			}
			catch (serr:Error)
			{
				Alert.show("Date Format Error! valid dates are in the format: 1/01/2010"+serr.getStackTrace());
			}
			
		}
		
		private function setComboChartBy(e:Event = null):void 
		{
			var l:Label = this.chartByLabels[e.currentTarget.id] as Label;
			var sl:String =  ComboBox(e.target).selectedLabel;
			var cbb:MultiSelectDropDownCtl = e.target as MultiSelectDropDownCtl;
			
			this.chartMessages.text = "Changing Chart By Values: From- "+ l.text +" To-"+sl;
			
			this.alChart.ResetChartData();
			//this.multipleKPIsSelected = false;
			this.chartHasCustomDatesSubChart = false;
			
			l.text = sl;
			
			//Alert.show("multipleKPIsSelected!"+l.text);
			
			if (cbb == this.chartByComboBoxes[0])
			{
				//Alert.show("Operator");
				if ((cbb.selectedItems.length > 1) && (l.text.indexOf("All") < 0))
				{
					l.text = "Some_Prdct_Nms";
				}
			}
			
			if (cbb == this.chartByComboBoxes[1])
			{
				//Alert.show("Shift");
			}
			
			if (cbb == this.chartByComboBoxes[2])
			{
				//Alert.show("Mon:"+l.text);
			}
			
			var dfcbb:MultiSelectDropDownCtl =  filterByComboBoxes[0] as MultiSelectDropDownCtl;
			var fcbb:MultiSelectDropDownCtl =  filterByComboBoxes[1] as MultiSelectDropDownCtl;
			var cb:ComboBox = filterByComboBoxes[1] as ComboBox;
			var a:Number = this.appLoader.GetChartDataKeys().indexOf("KPI_Type_Values");
			var kpiFilters:Array = this.appLoader.GetChartDataKeyValues()[a].split(',');
						
			//Alert.show("0"+cbb.selectedLabel);
			if (this.multipleKPIsSelected && (cbb.selectedItems.length==1))
			{
				/*
				//Alert.show("1"+cbb.selectedLabel);
				if ((cbb.selectedLabel == "All_Opr_Nms")||((cbb.selectedLabel == "All_Shifts")&&this.multipleKPIsSelected))
				{
					Alert.show("This version cannot select multiple Operators and multiple KPI at the same time(-5)");
				}
				else
				{
				*/
				var k:Number = 0;
				var kpi:String;
				if (fcbb.selectedItems[0] == "All_Kpi")
				{
					this.multipleKPIsSelected  = true;

					if (dfcbb.selectedItem != "All_Plants")
					{
						for (k = 1; k < this.chartFilters.length; k++ )
						{
							kpi = this.chartFilters[k];
							//l.text = kpi;
							//cb.selectedItem = this.chartFilters[k];
							cb.selectedIndex = k;
							
							if (k < 5)
							{
								// set the data map
								this.SetChartData(this.appLoader.GetChartData());
							}
							//Alert.show("label:"+l);
							//Alert.show(kpi);
						}
						
						cb.selectedItem = this.chartFilters[0];
					}
					else
					{
						
						// Hot Desk First
						for (k = 0; k < kpiFilters.length-4; k++ )
						{
							kpi = kpiFilters[k];
							//l.text = kpi;
							
							cb.selectedItem = kpiFilters[k];
							filterByComboBoxes[0].selectedItem = "CoGenPlant";
							
							// set the data map
							this.SetChartData(this.appLoader.GetChartData());
							
							//Alert.show("label:"+l);
							//Alert.show(cbb.selectedItems[k]);
						}
						
						// Cold Desk Second
						for (k = 4; k < kpiFilters.length; k++ )
						{
							kpi = kpiFilters[k];
							//l.text = kpi;
							
							cb.selectedItem = kpiFilters[k];
							filterByComboBoxes[0].selectedItem = "ASUPlant";
							
							// set the data map
							this.SetChartData(this.appLoader.GetChartData());
							
							//Alert.show("label:"+l);
							//Alert.show(cbb.selectedItems[k]);
						}
						
						this.cbs.selectedItem = this.chartFilters[0];
						filterByComboBoxes[0].selectedItem = "All_Plants";
						
					}
					
				}
				else
				{
					if (dfcbb.selectedItem != "All_Plants")
					{
											
						for (k = 0; k < fcbb.selectedItems.length; k++ )
						{
							kpi = fcbb.selectedItems[k];
							//l.text = kpi;
							cb.selectedItem = fcbb.selectedItems[k];
							
							// set the data map
							this.SetChartData(this.appLoader.GetChartData());
							
							//Alert.show("label:"+l);
							//Alert.show(cbb.selectedItems[k]);
						}
					}
					else
					{
						// Cold Desk First
						for (k = 0; k < fcbb.selectedItems.length; k++ )
						{
							//kpi = kpiFilters[k];
							//l.text = kpi;
							
							//cb.selectedIndex = k+1;
							cb.selectedItem = fcbb.selectedItems[k];
							filterByComboBoxes[0].selectedItem = "CoGenPlant";
							
							if (fcbb.selectedIndices[k] < 5)
							{
								// set the data map
								this.SetChartData(this.appLoader.GetChartData());
							}
							//Alert.show("label-k:"+cbb.selectedIndices[k]+" of "+cbb.selectedItems.length);
							//Alert.show(cbb.selectedItems[k]);
						}
							
						// Hot Desk Second
						for (k = 0; k < fcbb.selectedItems.length; k++ )
						{
							//kpi = kpiFilters[k];
							//l.text = kpi;
							
							//cb.selectedIndex = k+1;
							cb.selectedItem = fcbb.selectedItems[k];
							filterByComboBoxes[0].selectedItem = "ASUPlant";
							
							if (fcbb.selectedIndices[k] > 4)
							{
								// set the data map
								this.SetChartData(this.appLoader.GetChartData());
							}
							//Alert.show("label-k:"+cbb.selectedIndices[k]+" in "+cbb.selectedItems.length);
							//Alert.show(cbb.selectedItems[k]);
						}
						
						//cb.selectedItem = this.chartFilters[0];
						filterByComboBoxes[0].selectedItem = "All_Plants";
					}
				}
				//}
			}
			else
			{
				//Alert.show("2"+cbb.selectedLabel);
				if ((cbb.selectedItems.length > 1) && this.multipleKPIsSelected)
				{
					//Alert.show("This version cannot select multiple Operators and multiple KPI at the same time(-4)");
					//this.SetChartData(this.appLoader.GetChartData());
					if (fcbb.selectedItems[0] == "All_Kpi")
					{
						this.multipleKPIsSelected  = true;

						if (dfcbb.selectedItem != "All_Plants")
						{
							for (k = 1; k < this.chartFilters.length; k++ )
							{
								kpi = this.chartFilters[k];
								//l.text = kpi;
								cb.selectedIndex = k;
								
								// set the data map
								this.SetChartData(this.appLoader.GetChartData());
								
								//Alert.show("label:"+l);
								//Alert.show(kpi);
							}
							
							cb.selectedItem = this.chartFilters[0];
						}
						else
						{
							// Hot Desk First
							for (k = 0; k < kpiFilters.length-4; k++ )
							{
								kpi = kpiFilters[k];
								//l.text = kpi;
								
								cb.selectedItem = kpiFilters[k];
								filterByComboBoxes[0].selectedItem = "CoGenPlant";
								
								// set the data map
								this.SetChartData(this.appLoader.GetChartData());
								
								//Alert.show("label:"+l);
								//Alert.show(cbb.selectedItems[k]);
							}
							
							// Cold Desk Second
							for (k = 4; k < kpiFilters.length; k++ )
							{
								kpi = kpiFilters[k];
								//l.text = kpi;
								
								cb.selectedItem = kpiFilters[k];
								filterByComboBoxes[0].selectedItem = "ASUPlant";
								
								// set the data map
								this.SetChartData(this.appLoader.GetChartData());
								
								//Alert.show("label:"+l);
								//Alert.show(cbb.selectedItems[k]);
							}
							
							this.cbs.selectedItem = this.chartFilters[0];
							filterByComboBoxes[0].selectedItem = "All_Plants";
						}
					}
					else
					{
						if (dfcbb.selectedItem != "All_Plants")
						{
						
							for (k = 0; k < fcbb.selectedItems.length; k++ )
							{
								kpi = fcbb.selectedItems[k];
								//l.text = kpi;
								cb.selectedItem = fcbb.selectedItems[k];
								
								// set the data map
								this.SetChartData(this.appLoader.GetChartData());
								
								//Alert.show("label:"+l);
								//Alert.show(cbb.selectedItems[k]);
							}
						}
						else
						{
							// Cold Desk First
							for (k = 0; k < fcbb.selectedItems.length; k++ )
							{
								//kpi = kpiFilters[k];
								//l.text = kpi;
								
								//cb.selectedIndex = k+1;
								cb.selectedItem = fcbb.selectedItems[k];
								filterByComboBoxes[0].selectedItem = "CoGenPlant";
								
								if (fcbb.selectedIndices[k] < 5)
								{
									// set the data map
									this.SetChartData(this.appLoader.GetChartData());
								}
								//Alert.show("label-k:"+cbb.selectedIndices[k]+" of "+cbb.selectedItems.length);
								//Alert.show(cbb.selectedItems[k]);
							}
							
							// Hot Desk Second
							for (k = 0; k < fcbb.selectedItems.length; k++ )
							{
								//kpi = kpiFilters[k];
								//l.text = kpi;
								
								//cb.selectedIndex = k+1;
								cb.selectedItem = fcbb.selectedItems[k];
								filterByComboBoxes[0].selectedItem = "ASUPlant";
								
								if (fcbb.selectedIndices[k] > 4)
								{
									// set the data map
									this.SetChartData(this.appLoader.GetChartData());
								}
								//Alert.show("label-k:"+cbb.selectedIndices[k]+" in "+cbb.selectedItems.length);
								//Alert.show(cbb.selectedItems[k]);
							}
							
							//cb.selectedItem = this.chartFilters[0];
							filterByComboBoxes[0].selectedItem = "All_Plants";
						}
					}
					//Alert.show("after loop");
				}
				else
				{
					if (dfcbb.selectedItem == "All_Plants")
					{
						// Cold Desk First
							for (k = 0; k < fcbb.selectedItems.length; k++ )
							{
								//kpi = kpiFilters[k];
								//l.text = kpi;
								
								//cb.selectedIndex = k+1;
								cb.selectedItem = fcbb.selectedItems[k];
								filterByComboBoxes[0].selectedItem = "CoGenPlant";
								
								if (fcbb.selectedIndices[k] < 5)
								{
									// set the data map
									this.SetChartData(this.appLoader.GetChartData());
								}
								//Alert.show("label-k:"+cbb.selectedIndices[k]+" of "+cbb.selectedItems.length);
								//Alert.show(cbb.selectedItems[k]);
							}
							
							// Hot Desk Second
							for (k = 0; k < fcbb.selectedItems.length; k++ )
							{
								//kpi = kpiFilters[k];
								//l.text = kpi;
								
								//cb.selectedIndex = k+1;
								cb.selectedItem = fcbb.selectedItems[k];
								filterByComboBoxes[0].selectedItem = "ASUPlant";
								
								if (fcbb.selectedIndices[k] > 4)
								{
									// set the data map
									this.SetChartData(this.appLoader.GetChartData());
								}
								//Alert.show("label-k:"+cbb.selectedIndices[k]+" in "+cbb.selectedItems.length);
								//Alert.show(cbb.selectedItems[k]);
							}
							
							//cb.selectedItem = this.chartFilters[0];
							filterByComboBoxes[0].selectedItem = "All_Plants";
					}
					else
					{
						// set the data map
						this.SetChartData(this.appLoader.GetChartData());
						//Alert.show("no loop");
					}
				}
			}
		
				// finally draw the chart with the data
			this.DrawChart();
				
			//this.dbgText.text = "";
				
			// list all items selected
			//Alert.show("selected!"+cbb.selectedIndices);
			
			
		}
		
		private function setComboFilterBy(e:Event):void
		{
			var sl:String =  ComboBox(e.target).selectedLabel;
			var cbb:MultiSelectDropDownCtl = e.target as MultiSelectDropDownCtl;
			var cb:ComboBox = e.target as ComboBox;
			
			this.alChart.ResetChartData();
			this.chartHasCustomDatesSubChart = false;
			
			if (cbb == this.filterByComboBoxes[0])
			{
				//Alert.show("Plant");
			}
			
			if (cbb == this.filterByComboBoxes[1])
			{
				//Alert.show("KPI");
				
				if (cbb.selectedItems.length > 1)
				{
					// set the flag
					this.multipleKPIsSelected = true;
				}
				else
				{
					//this.alChart.ResetChartData();
					this.multipleKPIsSelected = false;
				}
				
				var dfcbb:MultiSelectDropDownCtl =  filterByComboBoxes[0] as MultiSelectDropDownCtl;
				var ccbb:MultiSelectDropDownCtl =  chartByComboBoxes[0] as MultiSelectDropDownCtl;
				var ccbb1:ComboBox =  chartByComboBoxes[1];
				var a:Number = this.appLoader.GetChartDataKeys().indexOf("KPI_Type_Values");
				var kpiFilters:Array = this.appLoader.GetChartDataKeyValues()[a].split(',');
			
				//if (ccbb.selectedIndices.length > 1) 
				{
					
					
					if ((cbb.selectedItems.length == 1))
					{
						//Alert.show("Yo:"+cbb.selectedItems[0]);
					}
					
					var k:Number = 0;
					var kpi:String;
					if (cbb.selectedItems[0] == "All_Kpi")
					{
						this.multipleKPIsSelected  = true;

						if (dfcbb.selectedItem != "All_Plants")
						{
							for (k = 1; k < this.chartFilters.length; k++ )
							{
								kpi = this.chartFilters[k];
								//l.text = kpi;
								//cb.selectedItem = this.chartFilters[k];
								cb.selectedIndex = k;
								
								if (k < 5)
								{
									// set the data map
									this.SetChartData(this.appLoader.GetChartData());
								}
								//Alert.show("label:"+l);
								//Alert.show(kpi);
							}
							
							cb.selectedItem = this.chartFilters[0];
						}
						else
						{
							// Hot Desk First
							for (k = 0; k < kpiFilters.length-4; k++ )
							{
								kpi = kpiFilters[k];
								//l.text = kpi;
								
								cb.selectedIndex = k+1;
								filterByComboBoxes[0].selectedItem = "CoGenPlant";
								
								// set the data map
								this.SetChartData(this.appLoader.GetChartData());
								
								//Alert.show("label:"+l);
								//Alert.show(cbb.selectedItems[k]);
							}
							
							// Cold Desk Second
							for (k = 4; k < kpiFilters.length; k++ )
							{
								kpi = kpiFilters[k];
								//l.text = kpi;
								
								cb.selectedIndex = k+1;
								filterByComboBoxes[0].selectedItem = "ASUPlant";
								
								// set the data map
								this.SetChartData(this.appLoader.GetChartData());
								
								//Alert.show("label:"+l);
								//Alert.show(cbb.selectedItems[k]);
							}
							
							cb.selectedItem = this.chartFilters[0];
							filterByComboBoxes[0].selectedItem = "All_Plants";
						}
					}
					else
					{
						if (dfcbb.selectedItem == "All_Plants")
						{
							// Cold Desk First
							for (k = 0; k < cbb.selectedItems.length; k++ )
							{
								//kpi = kpiFilters[k];
								//l.text = kpi;
								
								//cb.selectedIndex = k+1;
								cb.selectedItem = cbb.selectedItems[k];
								filterByComboBoxes[0].selectedItem = "CoGenPlant";
								
								if (cbb.selectedIndices[k] < 5)
								{
									// set the data map
									this.SetChartData(this.appLoader.GetChartData());
								}
								//Alert.show("label-k:"+cbb.selectedIndices[k]+" of "+cbb.selectedItems.length);
								//Alert.show(cbb.selectedItems[k]);
							}
							
							// Cold Desk Second
							for (k = 0; k < cbb.selectedItems.length; k++ )
							{
								//kpi = kpiFilters[k];
								//l.text = kpi;
								
								//cb.selectedIndex = k+1;
								cb.selectedItem = cbb.selectedItems[k];
								filterByComboBoxes[0].selectedItem = "ASUPlant";
								
								if (cbb.selectedIndices[k] > 4)
								{
									// set the data map
									this.SetChartData(this.appLoader.GetChartData());
								}
								//Alert.show("label-k:"+cbb.selectedIndices[k]+" in "+cbb.selectedItems.length);
								//Alert.show(cbb.selectedItems[k]);
							}
							
							//cb.selectedItem = this.chartFilters[0];
							filterByComboBoxes[0].selectedItem = "All_Plants";
						}
						else
						{
							for (k = 0; k < cbb.selectedItems.length; k++ )
							{
								kpi = cbb.selectedItems[k];
								//l.text = kpi;
								cb.selectedItem = cbb.selectedItems[k];
								
								// set the data map
								this.SetChartData(this.appLoader.GetChartData());
								
								//Alert.show("label:"+l);
								//Alert.show(cbb.selectedItems[k]);
							}
						}
					}
				}
			}
			
			// list all items selected
			//Alert.show("selected!" + cbb.selectedIndices);
			
			// finally draw the chart with the data
			this.DrawChart();
		}
		
		private function setChartDisplay(e:Event):void
		{
			var displayStyle:String =  ComboBox(e.target).selectedLabel;
			
			this.alChart.ResetChartData();
			//this.multipleKPIsSelected = false;
			//Alert.show(":->"+displayStyle);
			
			this.chartMessages.text = "Changing Display By Values: To-"+displayStyle;
			var htmlAlChart:HTMLChart;
			
			switch(displayStyle)
			{
				case "Bar":
					if (alChart is HTMLChart)
					{
						htmlAlChart = alChart as HTMLChart;
						htmlAlChart.DisplayFormat = ALChart.BAR;
						htmlAlChart.ThreeDView = false;
					}
					break;
				case "3D Bar":
					if (alChart is HTMLChart)
					{
						htmlAlChart = alChart as HTMLChart;
						htmlAlChart.DisplayFormat = ALChart.BAR;
						htmlAlChart.ThreeDView = true;
					}
					break;
					
				case "Line":
					this.alChart.DisplayFormat = ALChart.LINE;
					
					break;
					
				case "Column":
					this.alChart.DisplayFormat = ALChart.COLUMN;
					if (alChart is HTMLChart)
					{
						htmlAlChart = alChart as HTMLChart;
						htmlAlChart.DisplayFormat = ALChart.COLUMN;
						htmlAlChart.ThreeDView = false;
					}
					break;
				case "3D Column":
					this.alChart.DisplayFormat = ALChart.COLUMN;
					if (alChart is HTMLChart)
					{
						htmlAlChart = alChart as HTMLChart;
						htmlAlChart.DisplayFormat = ALChart.COLUMN;
						htmlAlChart.ThreeDView = true;
					}
					break;
				case "Column_Area":
					this.alChart.DisplayFormat = ALChart.COLUMN_AREA;
					
					break;
			};
			
			var dfcbb:MultiSelectDropDownCtl =  filterByComboBoxes[0] as MultiSelectDropDownCtl;
			var fcbb:MultiSelectDropDownCtl =  filterByComboBoxes[1] as MultiSelectDropDownCtl;
			var cb:ComboBox = filterByComboBoxes[1] as ComboBox;
			var a:Number = this.appLoader.GetChartDataKeys().indexOf("KPI_Type_Values");
			var kpiFilters:Array = this.appLoader.GetChartDataKeyValues()[a].split(',');
			
			if (this.multipleKPIsSelected)
			{
				if (fcbb.selectedItems[0] == "All_Kpi")
				{
					//this.multipleKPIsSelected  = true;
					if (dfcbb.selectedItem != "All_Plants")
					{
						for (k = 0; k < this.chartFilters.length; k++ )
						{
							kpi = this.chartFilters[k];
							//l.text = kpi;
							//cb.selectedItem = this.chartFilters[k];
							cb.selectedIndex  = k;
							
							if (k < 5)
							{
								// set the data map
								this.SetChartData(this.appLoader.GetChartData());
							}
							//Alert.show("label:"+l);
							//Alert.show(kpi);
						}
						
						cb.selectedItem = this.chartFilters[0];
					}
					else
					{
						// Cold Desk First
							for (k = 0; k < kpiFilters.length; k++ )
							{
								kpi = kpiFilters[k];
								//l.text = kpi;
								
								cb.selectedIndex = k+1;
								filterByComboBoxes[0].selectedItem = "CoGenPlant";
								
								// set the data map
								this.SetChartData(this.appLoader.GetChartData());
								
								//Alert.show("label:"+l);
								//Alert.show(cbb.selectedItems[k]);
							}
							
							// Hot Desk Second
							for (k = 0; k < kpiFilters.length; k++ )
							{
								kpi = kpiFilters[k];
								//l.text = kpi;
								
								cb.selectedIndex = k+1;
								filterByComboBoxes[0].selectedItem = "ASUPlant";
								
								// set the data map
								this.SetChartData(this.appLoader.GetChartData());
								
								//Alert.show("label:"+l);
								//Alert.show(cbb.selectedItems[k]);
							}
							
							cb.selectedItem = this.chartFilters[0];
							filterByComboBoxes[0].selectedItem = "All_Plants";
					}
				}
				else
				{
					if (dfcbb.selectedItem != "All_Plants")
					{
						for (var k:Number = 0; k < fcbb.selectedItems.length; k++ )
						{
							var kpi:String = fcbb.selectedItems[k];
							//l.text = kpi;
							cb.selectedItem = fcbb.selectedItems[k];
							
							// set the data map
							this.SetChartData(this.appLoader.GetChartData());
							
							//Alert.show("label:"+l);
							//Alert.show(cbb.selectedItems[k]);
						}
					}
					else
					{
						// Cold Desk First
							for (k = 0; k < fcbb.selectedItems.length; k++ )
							{
								//kpi = kpiFilters[k];
								//l.text = kpi;
								
								//cb.selectedIndex = k+1;
								cb.selectedItem = fcbb.selectedItems[k];
								filterByComboBoxes[0].selectedItem = "CoGenPlant";
								
								if (fcbb.selectedIndices[k] < 5)
								{
									// set the data map
									this.SetChartData(this.appLoader.GetChartData());
								}
								//Alert.show("label-k:"+cbb.selectedIndices[k]+" of "+cbb.selectedItems.length);
								//Alert.show(cbb.selectedItems[k]);
							}
							
							// Hot Desk Second
							for (k = 0; k < fcbb.selectedItems.length; k++ )
							{
								//kpi = kpiFilters[k];
								//l.text = kpi;
								
								//cb.selectedIndex = k+1;
								cb.selectedItem = fcbb.selectedItems[k];
								filterByComboBoxes[0].selectedItem = "ASUPlant";
								
								//if (fcbb.selectedIndices[k] > 4)
								{
									// set the data map
									this.SetChartData(this.appLoader.GetChartData());
								}
								//Alert.show("label-k:"+cbb.selectedIndices[k]+" in "+cbb.selectedItems.length);
								//Alert.show(cbb.selectedItems[k]);
							}
							
							//cb.selectedItem = this.chartFilters[0];
							filterByComboBoxes[0].selectedItem = "All_Plants";
					}
				}
			}
			else
			{
				if (dfcbb.selectedItem == "All_Plants")
				{
					for (k = 0; k < fcbb.selectedItems.length; k++ )
							{
								//kpi = kpiFilters[k];
								//l.text = kpi;
								
								//cb.selectedIndex = k+1;
								cb.selectedItem = fcbb.selectedItems[k];
								filterByComboBoxes[0].selectedItem = "CoGenPlant";
								
								if (fcbb.selectedIndices[k] < 5)
								{
									// set the data map
									this.SetChartData(this.appLoader.GetChartData());
								}
								//Alert.show("label-k:"+cbb.selectedIndices[k]+" of "+cbb.selectedItems.length);
								//Alert.show(cbb.selectedItems[k]);
							}
							
							// Hot Desk Second
							for (k = 0; k < fcbb.selectedItems.length; k++ )
							{
								//kpi = kpiFilters[k];
								//l.text = kpi;
								
								//cb.selectedIndex = k+1;
								cb.selectedItem = fcbb.selectedItems[k];
								filterByComboBoxes[0].selectedItem = "ASUPlant";
								
								//if (fcbb.selectedIndices[k] > 4)
								{
									// set the data map
									this.SetChartData(this.appLoader.GetChartData());
								}
								//Alert.show("label-k:"+cbb.selectedIndices[k]+" in "+cbb.selectedItems.length);
								//Alert.show(cbb.selectedItems[k]);
							}
							
							//cb.selectedItem = this.chartFilters[0];
							filterByComboBoxes[0].selectedItem = "All_Plants";
				}
				else
				{
					// set the data map
					this.SetChartData(this.appLoader.GetChartData());
				}
			}
			
			// finally draw the chart with the data
			this.DrawChart();
			
		}
		
		
		private function setChartGenerator(e:Event):void
		{
			var generationStyle:String =  ComboBox(e.target).selectedLabel;
			
			//Alert.show(":->"+generationStyle);
			
			this.chartMessages.text = "Changing Generated By Value: To-"+generationStyle;
			
			switch(generationStyle)
			{
				case "FLEX":
					//this.alChart.Renderer = ALChart.FLEX;
					this.alChart.Renderer = ALChart.AM; // TODO: Hack!!!
					break;
					
				case "AM":
					this.alChart.Renderer = ALChart.AM;
					
					break;
			};
			
			// set the data map
			this.SetChartData(this.appLoader.GetChartData());
				
			// finally draw the chart with the data
			this.DrawChart();
			
		}
		
		private function showStartDateChooser(e:MouseEvent):void
		{
			this.chartStartChooser.visible = true;
		}
		
		private function showEndDateChooser(e:MouseEvent):void
		{
			this.chartEndChooser.visible = true;
		}
		
		private function hideStartDateChooser(e:MouseEvent):void
		{
			this.chartStartChooser.visible = false;
		}
		
		private function hideEndDateChooser(e:MouseEvent):void
		{
			this.chartEndChooser.visible = false;
		}
		
		private function showSelectedStartDate(e:CalendarLayoutChangeEvent):void
		{
			var dc:DateChooser = DateChooser(e.target);
			this.chartStartDate.label = (dc.selectedDate.getMonth() + 1).toString() + "/" + dc.selectedDate.getDate() +"/" + dc.selectedDate.getFullYear().toString();
			dc.visible = false;
		}
		
		private function showSelectedEndDate(e:CalendarLayoutChangeEvent):void
		{
			var dc:DateChooser = DateChooser(e.target);
			this.chartEndDate.label = (dc.selectedDate.getMonth() + 1).toString() + "/" + dc.selectedDate.getDate() +"/" + dc.selectedDate.getFullYear().toString();
			dc.visible = false;
		}
		
		private function addFiltersToHeader():void
		{
			// initialize filter
			this.chartFilter = new MultiSelectDropDownCtl();
			
			// set Label
			var filterHeader:Label = new Label();
			filterHeader.text = "filter By";
			
			// add Data
			chartFilter.dataProvider = this.chartFilters;
			//chartFilter.height = 200;
			
			chartFilter.selectedIndex = 0;
			
			chartFilter.addEventListener(DropdownEvent.CLOSE,reDrawChart);
			
			//chartFilter.addEventListener(DropdownEvent.OPEN,reDrawChart);
			
			// add to Display Array
			filterByComboBoxes.push(chartFilter);
		}
		
		private function drawSubChart(idNum:Number):void
		{
			// initialize selector
			this.chartSelector = new VBox();
			this.chartSelector.id = idNum.toString();
			var selectorHeader:Label = new Label();
			selectorHeader.text = "Chart By";
			selectorHeader.setStyle("fontWeight", "bold");
			
			this.chartSelector.addChild(selectorHeader);
			
			for (var i:Number = 0; i < this.chartSubCharts.length; i++ )
			{
				// load one of the vboxes
				if (i == 0)
				{
					this.chartByLabel = new Label();
					//this.chartByComboBox = new ComboBox();
					
					this.chartByLabel.text = chartSubCharts[i];
					
					// add to array for later
					chartByLabels.push(chartByLabel);
					//chartByComboBoxes.push();
				}
				
				this.chartSubChart = new LinkButton();
				this.chartSubChart.label = chartSubCharts[i];
				this.chartSubChart.setStyle("color", 0x66CCFF);
				this.chartSubChart.id = i.toString();
				this.chartSubChart.addEventListener(MouseEvent.CLICK,setChartBy);
				
				//this.chartSelector.addChild(this.chartSubChart);
			}
			
			// add it to the HBox on the left
			this.chartSelectors.addChild(this.chartSelector);
			
		}
		
		private function drawHeader():void
		{
			var l:Label = new Label();
			var l1:Label = new Label();
			var l2:Label = new Label();
			var l3:Label = new Label();
			var l4:Label = new Label();
			var l5:Label = new Label();
			var l6:Label = new Label();
			var l7:Label = new Label();
			
			// add labels
			for (var i:Number = 0; i < this.chartByLabels.length; i++ )
			{
				l = new Label();
				//l.text = "Chart By:";
				l.setStyle("fontWeight", "bold");
				this.chartHeader.addChild(l);
				//this.chartHeader.addChild(this.chartByLabels[i]);
				this.chartHeader.addChild(this.chartByComboBoxes[i]);
			}
			
			l1 = new Label();
			l1.x = 100;
			l1.y = 5;
			l1.text = "Product";
			
			l2 = new Label();
			l2.x = 290;
			l2.y = 5;
			l2.text = "Pipeline";
			
			l3 = new Label();
			l3.x = 400;
			l3.y = 5;
			l3.text = "Fixed Date Range";
			
			this.addChild(l1);
			this.addChild(l2);
			this.addChild(l3);
			
			// add filter combo boxes
			for (var j:Number = 0; j < this.filterByComboBoxes.length; j++ )
			{
				l = new Label();
				//l.text = "Filtering By:";
				l.setStyle("fontWeight", "bold");
				this.chartHeader.addChild(l);
				this.chartHeader.addChild(this.filterByComboBoxes[j]);
			}
			
			l4 = new Label();
			l4.x = 575;
			l4.y = 5;
			l4.text = "Plant";
			
			l5 = new Label();
			l5.x = 725;
			l5.y = 5;
			l5.text = "KPI";
			
			l6 = new Label();
			l6.x = 800;
			l6.y = 5;
			l6.text = "Chart Type";
			
			this.addChild(l4);
			this.addChild(l5);
			
			
			// description Label
			l = new Label();
			//l.text = "Display By:";
			l.setStyle("fontWeight", "bold");
			this.chartHeader.addChild(l);
				
			// add styles
			this.chartHeader.addChild(this.chartDisplays);
			
			l6 = new Label();
			l6.x = 880;
			l6.y = 5;
			l6.text = "Chart Type";
			
			this.addChild(l6);
			
			// description Label
			l = new Label();
			//l.text = "Generated By:";
			l.setStyle("fontWeight", "bold");
			this.chartHeader.addChild(l);
				
			// add styles
			//this.chartHeader.addChild(this.chartStyles);
		}
		
	}
	
}