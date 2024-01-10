package com.airliquide.alps.chart.type 
{
	// AS3 SDK
	
	import com.airliquide.alps.chart.kpi.KpiLegend;
	import com.amcharts.AmLegend;
	import com.amcharts.axes.CategoryAxis;
	import com.amcharts.axes.ValueAxis;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.filters.DropShadowFilter;
	import flash.text.TextField;
	import flash.utils.*;
	
	// FLEX SDK
	//import mx.charts.*;
	import mx.collections.*;
	import mx.containers.*;
	import mx.graphics.*;
	import mx.controls.*;
	import mx.charts.*;
	import mx.charts.chartClasses.*;
	import mx.collections.ArrayCollection;
	import mx.utils.ObjectUtil;
	import mx.core.UIComponent;
	import mx.charts.series.LineSeries;
	import mx.charts.series.AreaSeries;
	import mx.charts.series.ColumnSeries;
	
	// AM SDK
	import com.amcharts.AmSerialChart;
	import com.amcharts.chartClasses.*;
	import com.amcharts.events.*;
	
	// Adobe SDK
	import adobe.utils.CustomActions;
	
	// Airliquide SDK
	import com.airliquide.alps.chart.ALChart;
	import com.airliquide.alps.chart.display.*;
	import com.airliquide.alps.lang.*;
	import com.airliquide.alps.chart.data.FormatParser;
	
	
	/**
	 * @author earl.lawrence
	 * Class is responsible for drawing to the 
	 * movie using a given rendering engine. Primary input for this is 
	 * Variables, so it need to understand the variable format
	 * and what the categories mean.
	 */
	public class HTMLChart extends ALChart
	{
		// al framework values
		private var renderingEngine:int;
		private var chart:ChartDisplayFormat;
		private var dataParser:FormatParser;
		
		// charting gui stuffs
		private var chartPanel:Panel;
		private var chartGrid:DataGrid;
		private var sc:SolidColor;
		private var stroke:Stroke;
		
		// generic data containers
		private var chartColors:Array;
		private var chartStrokes:Array;
		private var chartData:Map;
		private var chartDisplayObjects:Array;
		private var chartDataArray:ArrayCollection;
		
		// debug text
		private var dbgText:Label;
		
		public function HTMLChart(renderer:int) 
		{
			super();
			
			// al framework stuffs
			this.renderingEngine = renderer;
			this.Renderer = renderer;
			this.dataParser = new FormatParser();
			
			// Flex related attribute arrays
			this.chartColors = new Array();
			this.chartStrokes = new Array();
						
			// Gui Stuffs
			chartDisplayObjects = new Array();
			
			// Debug
			this.dbgText = new Label();
			dbgText.x = 400;
			dbgText.y = 300;
			dbgText.width = 1000;
			dbgText.height = 100;
			
			switch (renderingEngine)
			{
				case ALChart.FLEX:
					
					// set Color(s)
					this.setRelevantColors();
					
					// set Stroke
					this.setRelevantStrokes();
					
					// done
					break;
			}
			
			
		}
		
		private function zoom(chart:AmSerialChart):void
		{
			chart.zoomToIndexes(0,20);
		}
		
		public function SetChartData(d:Map):void
		{
			this.chartData = d;
		}
		
		public function Draw():void
		{
			//this.dbgText.text = "";
			var flexLabel:Label = new Label();
			var ca:mx.charts.CategoryAxis = new mx.charts.CategoryAxis();
			var asc:AmSerialChart;
			var asc_raw:AmSerialChart;
			var aca:com.amcharts.axes.CategoryAxis = new com.amcharts.axes.CategoryAxis();
			var ccur:ChartCursor = new ChartCursor();
			
			dataParser = new FormatParser();
			
			if (this.chart is com.airliquide.alps.chart.display.Bar)
			{
				// set panel
				if (this.chartPanel == null)
				{
					this.chartPanel = new Panel();
				}
				
				this.chartPanel.title = "Air Liquide Charting: HTML Bar Chart:FLEX ";
				this.chartPanel.percentHeight = 100;
				this.chartPanel.percentWidth = 85;
				this.chartPanel.layout = "vertical";
				this.chartPanel.x = 50;
				this.chartPanel.y = 225;
				this.chartPanel.setStyle("horizontalAlign", "center");
				this.chartPanel.setStyle("verticalAlign","middle");
				this.chartPanel.setStyle("paddingTop", 50);
				this.chartPanel.name = "pCharting";
				this.chartPanel.removeAllChildren();

				
				if (this.Renderer == ALChart.FLEX)
				{
					var flexBarChart:com.airliquide.alps.chart.display.Bar = this.chart as com.airliquide.alps.chart.display.Bar;
				
				
					// set axis category
					//Alert.show(this.GetYLabel());
					//ca.categoryField = "Country";
					ca.categoryField = this.GetYLabel();//"Y_Label";
					flexBarChart.SetYAxisCategory(ca);
					
					// set up the series information
					flexBarChart.SetSeries(this.dataParser.ParseCurrentData(FormatParser.FROM_CURRENT_DATA_TO_BARSERIES_ARRAY,this.GetXLabel(),this.GetYLabel()) as Array);
					//this.dbgText.text += this.dataParser.GetDebugText();
					
					// draw the chart
					flexBarChart.DrawBarChart();
					
					var bc:ChartBase = flexBarChart.GetFlexChart() as BarChart;
					bc.percentHeight = 50;
					bc.percentWidth = 75;
					bc.showDataTips = true;
					
					//this.dbgText.text += flexBarChart.dbgText.text;
					
					// add chart
					this.chartPanel.addChild(bc);
				}
				
				if (this.Renderer == ALChart.AM)
				{
					this.chartPanel.title = "Air Liquide Charting: HTML Bar Chart:AM ";
					var amBarChart:com.airliquide.alps.chart.display.Bar = this.chart as com.airliquide.alps.chart.display.Bar;
					var bg:AmGraph = new AmGraph();
					
					var amLegend:KpiLegend = new KpiLegend();
					var agrkey:KpiLegend = new KpiLegend();
					
					var bgs:Array = new Array();
					var bgsv:Array = new Array();
					
					var lrgs:Array = new Array();
					var lrgsv:Array = new Array();
					var lrgsd:ArrayCollection = new ArrayCollection();
					var d_raw:Dictionary = new Dictionary();
					
					var btestac:ArrayCollection = amBarChart.GetArrayCollectionData();
					var bava:ValueAxis = new com.amcharts.axes.ValueAxis();
					var e:Number = 0;
					var combobNum:Number = btestac.length;
					var test:Dictionary;
					var kpiCounters:Dictionary = new Dictionary();
					
					bava.setStyle("axisThickness", 2);
					bava.stackType = "regular";
								
					if (amBarChart.GetTotalDateAndValueCollectionData() != null)
					{
						if (amBarChart.GetArrayCollectionData() != null)
						{
							if (this.ChartArrayValues)
							{
								combobNum = this.GetKPISetTotal();
							}
							//var btestac:ArrayCollection = amBarChart.GetArrayCollectionData();
							//Alert.show("collection data length="+amBarChart.GetTotalDateAndValueCollectionData().length);
							//Alert.show("here:"+combobNum);
							for (e=0; e < combobNum; e++ )
							{
								
								bg = new AmGraph();
								bg.valueField = e.toString();
								//bg.valueField = "";
								bg.type = "column";
								bg.setStyle("fillAlphas", [0.75]);
								
								
								if (this.ChartArrayValues)
								{
									data = this.GetKPISetValues()[e]; 
									if (data != null)
									{
										//Alert.show("was not null:"+data["KPI_Name"]);
										this.setAmChartKpiLabelLineColor(data["KPI_Name"],bg, false);
									}
									else
									{
										//Alert.show("was Null");
									}
								}
								else
								{
									data = btestac[e];
									if (data != null)
									{
										bg.title = data[this.GetYLabel()] + " Aggregate";
										bg.balloonText = "Actual " + this.GetKPIName() +" Score is [[value]]";
										this.setAmChartKpiLabelLineColor(this.GetKPIName(), bg, true);
									}
								}
							
								bg.addEventListener(GraphEvent.ROLL_OVER_GRAPH_ITEM, agrkey.HandleChartMouseOver);
								bg.addEventListener(GraphEvent.CLICK_GRAPH_ITEM, agrkey.HandleChartClick);
								bg.addEventListener(GraphEvent.ROLL_OUT_GRAPH_ITEM, agrkey.HandleChartMouseOut);
							
								bgs.push(bg);
								
								bava = new com.amcharts.axes.ValueAxis();
								bava.setStyle("axisThickness", 2);
								bava.stackType = "regular";
					
								bgsv.push(bava);
							}
							
							
							bg = new AmGraph();
							bg.valueField = "max";
							bg.type = "column";
							bg.setStyle("lineColor","#00ABC0");
							bg.setStyle("fillAlphas", [0.10]);
							
							if (this.ChartArrayValues)
							{
								bg.balloonText = "Maximum Score Possible for KPI(s) Selected Includes [[value]] Additional Points.";
								bg.title = "Max Possible Score";
							}
							else
							{
								bg.balloonText = "Maximum Score Possible for KPI(s) Selected Includes [[value]] Additional Points.";
								bg.title = this.GetKPIName() + " Max Possible Score";
								
							}
							
								
							bgs.push(bg);
							
							bava = new com.amcharts.axes.ValueAxis();
							bava.setStyle("axisThickness", 2);
							bava.stackType = "regular";
							bava.setStyle("gridAlpha","0.1");
				
							bgsv.push(bava);
						}
						
						var testac:ArrayCollection = amBarChart.GetTotalDateAndValueCollectionData();
						//Alert.show("collection data length="+amBarChart.GetTotalDateAndValueCollectionData().length);
						for (var c:Number=0; c < testac.length; c++ )
						{
							var test2ac:ArrayCollection = testac.getItemAt(c).coll as ArrayCollection;
							
							if (test2ac != null)
							{
								for (var d:Number = 0; d < test2ac.length; d++ )
								{
									d_raw = new Dictionary();
									d_raw["score_" + c] = test2ac.getItemAt(d).score;
									d_raw["date"] = test2ac.getItemAt(d).date;
									lrgsd.addItem(d_raw);
									//Alert.show("Date=" + d_raw["date"] + "&Score" + test2ac.getItemAt(d).score);
								}
							
								//Alert.show("we are here");
								
								var ava_raw:ValueAxis = new com.amcharts.axes.ValueAxis();
								ava_raw.setStyle("axisThickness", 2);
								
								var lgr:AmGraph = new AmGraph();
								lgr.valueField = "score_" + c;
								lgr.type = "smoothedLine";
								//lgr.addEventListener(GraphEvent.ROLL_OVER_GRAPH_ITEM, amLegend.HandleChartMouseOver);
								
								if (this.ChartArrayValues)
								{
									lgr.title = testac.getItemAt(c).kpi + " Score For " + testac.getItemAt(c).cat;
									lgr.balloonText = "Score is [[value]]";
									
									this.setAmChartArrayKpiLabelSymbolColor(testac.getItemAt(c).kpi, testac.getItemAt(c).cat,kpiCounters, lgr, false);
								}
								else
								{
									this.setAmChartArrayKpiLabelSymbolColor(this.GetKPIName(), this.GetKPICategory()[c], kpiCounters, lgr, true);
								}
								
								lrgs.push(lgr);
								
								lrgsv.push(ava_raw);
							}
						}
					}
					else
					{
						if (btestac.length > 0)
						{
							if (btestac[0]["0"] == null)
							{
									bg.valueField = this.GetXLabel();
									bg.type = "column";
									bg.negativeBase = new Number(009900);
									//bg.fillColorsField = "#CC0000";
									
									this.setAmChartKpiLabelLineColor(this.GetKPIName(), bg, true);
									
									bg.setStyle("fillAlphas", [0.75]);
									bg.title = "KPI Score for " + this.GetKPIName();
									bg.balloonText = "Actual KPI Score [[value]]";
									bgs.push(bg);
									
									bg = new AmGraph();
									bg.valueField = "max";
									bg.type = "column";
									bg.negativeBase = new Number(009900);
									//bg.fillColorsField = "#0000DD";
									bg.setStyle("fillAlphas", [0.10]);
									bg.title = this.GetKPIName() + " Max Possible Score";
									bg.balloonText = "Maximum Score Possible for KPI Selected Includes [[value]] Additional Points";
									bgs.push(bg);
									
									bava = new com.amcharts.axes.ValueAxis();
									bava.setStyle("axisThickness", 2);
									bava.stackType = "regular";
									bava.setStyle("gridAlpha","0.1");
						
									bgsv.push(bava);
							}
							else
							{
								if (amBarChart.GetArrayCollectionData() != null)
								{
									//if (btestac["KPI_Name"] == null)
									{
										//var btestac:ArrayCollection = amBarChart.GetArrayCollectionData();
										//Alert.show("collection data length="+amBarChart.GetTotalDateAndValueCollectionData().length);
										//Alert.show("collection data length="+amBarChart.GetArrayCollectionData().length);
										//Alert.show("collection data length="+btestac.length);
										//Alert.show("collection data length="+this.GetKPISetTotal());
										if (this.ChartArrayValues)
										{
											for (var f:Number=0; f < this.GetKPISetTotal(); f++ )
											{
												
												//Alert.show("f num:"+f.toString()+":"+btestac[1][f.toString()]);
												bg = new AmGraph();
												bg.valueField = f.toString();
												bg.type = "column";
												//bg.title = "Scores"
												bg.title = "Score For " + this.GetKPISetValues()[f]["KPI_Name"] as String;
												bg.balloonText = (this.GetKPISetValues()[f]["KPI_Name"] as String) + " Score is [[value]]";
												bg.negativeBase = new Number(009900);
												//bg.fillColorsField = "#CC0000";
												
												this.setAmChartKpiLabelLineColor(this.GetKPISetValues()[f]["KPI_Name"], bg, true);
												
												bg.setStyle("fillAlphas", [0.75]);
												//bg.balloonText = "testin this ";
												//bg.valueAxis = bava;
												
												bgs.push(bg);
												
												bava = new com.amcharts.axes.ValueAxis();
												bava.setStyle("axisThickness", 2);
												bava.stackType = "regular";
									
												bgsv.push(bava);
												
											}
										}
										else
										{
											for (e=0; e < btestac.length; e++ )
											{
												
												//Alert.show("e num:"+e.toString()+":"+btestac[1]["0"]);
												bg = new AmGraph();
												bg.valueField = e.toString();
												bg.type = "column";
												//bg.title = "Scores"
												bg.title = "Score For " + this.GetKPIName() ;
												bg.negativeBase = new Number(009900);
												//bg.fillColorsField = "#CC0000";
												bg.setStyle("fillAlphas", [0.75]);
												
												this.setAmChartKpiLabelLineColor(this.GetKPIName(), bg, true);
												
												//bg.valueAxis = bava;
												
												bgs.push(bg);
												
												bava = new com.amcharts.axes.ValueAxis();
												bava.setStyle("axisThickness", 2);
												bava.stackType = "regular";
									
												bgsv.push(bava);
												
											}
										}
										
										bg = new AmGraph();
										bg.valueField = "max";
										bg.type = "column";
										bg.negativeBase = new Number(009900);
										//bg.fillColorsField = "#0000DD";
										bg.setStyle("fillAlphas", [0.10]);
										bg.title = "Maximum Possible Score ";
										bg.balloonText = "Maximum Score Possible for KPI Selected Includes [[value]] Additional Points";
										bgs.push(bg);
										
										bava = new com.amcharts.axes.ValueAxis();
										bava.setStyle("axisThickness", 2);
										bava.stackType = "regular";
										bava.setStyle("gridAlpha","0.1");
							
										bgsv.push(bava);
									}
								}
							}
						}
					}
					
					asc = new AmSerialChart();
					//asc.percentWidth = 95;
					asc.width = 750;
					asc.percentHeight = 95;
					//asc.height = 250;
					asc.dataProvider = amBarChart.GetArrayCollectionData();
					asc.categoryField = this.GetYLabel();
					asc.graphs = bgs;
					
					//var aca:com.amcharts.axes.CategoryAxis = new com.amcharts.axes.CategoryAxis();
					//aca.parseDates = true;
					//aca.minPeriod = "YYYY";
					//aca.gridCountReal = new Number(15);
					
					asc.categoryAxis = aca;
					asc.valueAxes = bgsv;
					
					//var ccur:ChartCursor = new ChartCursor();
					ccur.useHandCursor = true;
					
					asc.chartCursor = ccur;
					if (this.ThreeDView)
					{
						asc.setStyle("angle", "45");
						asc.setStyle("depth3D", "25");
					}
					
					asc.setStyle("plotAreaFillColors",[0xFAFAFA]);
					asc.rotate = true;
					
					var sideBySide:HBox = new HBox();
					var onTop:VBox = new VBox();
					
					// add keys for everything
					//var agrkey:AmLegend = new AmLegend();
					//agrkey.dataProvider = asc;
					agrkey.percentWidth = 40;
					//agrkey.textClickEnabled = false;
					agrkey.setStyle("marginRight", "10");
					//agrkey.setStyle("marginLeft", "5");
					//agrkey.setStyle("marginBottom", "5");
					//agrkey.SetDataProvider(amBarChart.GetArrayCollectionData(),"ArrayCollection");
					agrkey.SetDataProvider(asc.graphs, "GraphArray");		
					
					var aggrhb:HBox = new HBox();
					aggrhb.percentHeight = 75;
					aggrhb.addChild(agrkey);
					aggrhb.addChild(asc);
					
					this.chartPanel.addChild(aggrhb);
					// add chart
					//chartPanel.addChild(asc);
					//onTop.addChild(asc);
					/*
					var amLegend:AmLegend = new AmLegend();
					amLegend.percentWidth = 25;
					amLegend.textClickEnabled = false;
					*/
					
					//amLegend.setStyle("marginRight", "5");
					//amLegend.setStyle("marginLeft", "10");
					//amLegend.setStyle("marginBottom", "15");
					
					if (amBarChart.GetTotalDateAndValueCollectionData() != null)
					{
						// set category axis
						var aca_raw:com.amcharts.axes.CategoryAxis = new com.amcharts.axes.CategoryAxis();
						aca_raw.parseDates = true;
						aca_raw.minPeriod = "DD";
						//aca_raw.gridCountReal = 15;
						
						// set chart cursor
						var acc_raw:ChartCursor= new ChartCursor();
						acc_raw.cursorPosition= "mouse";
						
						// set chart cursor
						var acsb_raw:ChartScrollbar= new ChartScrollbar();
						acsb_raw.height = 20;
						
						try
						{
							asc_raw = new AmSerialChart();
							//asc_raw.d
							asc_raw.percentWidth = 100;
							//asc_raw.percentHeight = 75;
							//asc_raw.height = 300;
							//asc_raw.dataProvider = amBarChart.GetTotalDateAndValueCollectionData();
							asc_raw.dataProvider = lrgsd;
							//asc_raw.dataProvider = chartDataTest;
							asc_raw.categoryField = "date";
							
							asc_raw.graphs = lrgs;
							asc_raw.valueAxes = lrgsv;
							//asc_raw.addValueAxis(lrgsv[0]);
							asc_raw.categoryAxis = aca_raw;
							asc_raw.chartCursor = acc_raw;
							asc_raw.chartScrollbar = acsb_raw;
							
							
							if (this.ChartArrayValues)
							{
								//amLegend.dataProvider = asc_raw;
								amLegend.SetDataProvider(this.GetKPISetValues(),"Array");
								asc_raw.width = 650;
							
								sideBySide.percentHeight = 55;
								sideBySide.addChild(amLegend);
								sideBySide.addChild(asc_raw);
								
								if (amBarChart.GetTotalDateAndValueCollectionData().length > 0)
								{
									this.chartPanel.addChild(sideBySide);
								}
								//this.chartPanel.addChild(asc_raw);
							}
							else
							{
								this.chartPanel.addChild(asc_raw);
							}
						}
						catch (er:Error)
						{
							Alert.show("we have problems:"+er.message);
						}
						
						//this.chartPanel.addChild(asc_raw);
						//onTop.addChild(asc_raw);
					}
					
					//sideBySide.addChild(onTop);
					//sideBySide.addChild(amLegend);
					
					//this.chartPanel.addChild(sideBySide);
					//this.chartPanel.addChild(asc);
				}
				
				this.chartGrid = new DataGrid();
				
				this.chartGrid.dataProvider = chartDataArray;
				this.chartGrid.percentHeight = 35;
				this.chartGrid.percentWidth = 75;
				this.chartGrid.allowMultipleSelection = true;
				
				// add chart
				//this.chartPanel.addChild(this.chartGrid);
				
				// add panel
				//this.chartDisplayObjects.push(this.dbgText);
				this.chartDisplayObjects.push(this.chartPanel);
			}
			
			if (this.chart is com.airliquide.alps.chart.display.Line)
			{
				//Alert.show("still a line");
				
				// set panel
				if (this.chartPanel == null)
				{
					this.chartPanel = new Panel();
				}
				
				this.chartPanel.title = "Air Liquide Charting: HTML Line Chart:FLEX ";
				this.chartPanel.percentWidth = 85;
				this.chartPanel.percentHeight = 100;
				this.chartPanel.layout = "vertical";
				this.chartPanel.x = 50;
				this.chartPanel.y = 220;
				this.chartPanel.setStyle("horizontalAlign", "center");
				this.chartPanel.setStyle("verticalAlign","middle");
				this.chartPanel.setStyle("paddingTop",50);
				this.chartPanel.removeAllChildren();
		
				if (this.Renderer == ALChart.FLEX)
				{
					
					
					//Alert.show("using Flex");
					try
					{
						var flexLineChart:Line = this.chart as com.airliquide.alps.chart.display.Line;
						
						
						// set axis category
						//Alert.show(this.GetYLabel());
						
						//ca = new CategoryAxis();
						//ca.categoryField = this.GetYLabel();
						//flexLineChart.SetYAxisCategory(ca);
						ca = new mx.charts.CategoryAxis();
						ca.categoryField = this.GetYLabel();
						flexLineChart.SetXAxisCategory(ca);
						
						if (this.dataParser != null)
						{
							// set up the series information
							//flexBarChart.SetSeries(this.dataParser.ParseCurrentData(FormatParser.FROM_CURRENT_DATA_TO_BARSERIES_ARRAY,this.GetXLabel(),this.GetYLabel()) as Array);
							flexLineChart.SetSeries(this.dataParser.ParseCurrentData(FormatParser.FROM_CURRENT_DATA_TO_LINESERIES_ARRAY,this.GetYLabel(),this.GetXLabel()) as Array);
							//this.dbgText.text += this.dataParser.GetDebugText();
						}
						
						
						var lSeries:LineSeries = new LineSeries();
						//lSeries.xField = "Score For";
						lSeries.yField = "Score";
						//lSeries.displayName = "Score For";
						var sArray:Array = new Array();
						sArray.push(lSeries);
						flexLineChart.SetSeries(sArray);
						
						// initialize & draw the chart
						flexLineChart.DrawLineChart();
						
						
						var lc:ChartBase = flexLineChart.GetFlexChart();
						lc.percentHeight = 75;
						lc.percentWidth = 75;
						lc.showDataTips = true;
						
						// add chart
						this.chartPanel.addChild(lc);
						
					
					}
					catch (err:Error)
					{
						Alert.show("[ERROR] problems redinring chart in FLEX:"+err.message);
					}
				}
				
				if (this.Renderer == ALChart.AM)
				{
					this.chartPanel.title = "Air Liquide Charting: HTML Line Chart:AM";
					var amLineChart:com.airliquide.alps.chart.display.Line = this.chart as com.airliquide.alps.chart.display.Line;
				
				
					// set axis category
					//Alert.show("AM:"+this.GetYLabel());
					//ca.categoryField = "Country";
					//ca.categoryField = this.GetYLabel();//"Y_Label";
					//amBarChart.SetYAxisCategory(ca);
					//amBarChart.SetXAxisValueField(this.GetXLabel());
					
					// draw the chart
					//amBarChart.DrawBarChart();
					
					var lg:AmGraph = new AmGraph();
					lg.valueField = this.GetXLabel();
					lg.type = "line";
					lg.negativeBase = new Number(009900);
					//lg.fillColorsField = "#000000";
					lg.setStyle("fillAlphas",[0]);
					lg.setStyle("lineThickness",2);
					lg.bulletField = "round";
					var dsf:DropShadowFilter = new DropShadowFilter(2, 45, 0, 0.5);
					lg.filters = [dsf];
					
					//this.dbgText.text += flexBarChart.dbgText.text;
					var lgs:Array = new Array();
					lgs.push(lg);
					
					asc = new AmSerialChart();
					asc.percentWidth = 75;
					asc.percentHeight = 75;
					asc.dataProvider = amLineChart.GetArrayCollectionData();
						
					asc.categoryField = this.GetYLabel();
					asc.graphs = lgs;
										//var aca:com.amcharts.axes.CategoryAxis = new com.amcharts.axes.CategoryAxis();
					//aca.parseDates = true;
					//aca.minPeriod = "YYYY";
					//aca.gridCountReal = new Number(15);
					
					asc.categoryAxis = aca;
					
					//var ccur:ChartCursor = new ChartCursor();
					ccur.useHandCursor = true;
					
					asc.chartCursor = ccur;
					if (this.ThreeDView)
					{
						asc.setStyle("angle", "30");
						asc.setStyle("depth3D", "20");
					}
					asc.setStyle("plotAreaFillAlphas",[0]);
					//asc.rotate = true;
					
					// add chart
					this.chartPanel.addChild(asc);
				}
				
				
				this.chartGrid = new DataGrid();
				
				this.chartGrid.dataProvider = chartDataArray;
				this.chartGrid.percentHeight = 40;
				this.chartGrid.percentWidth = 100;
				this.chartGrid.allowMultipleSelection = true;
				
				// add chart
				//this.chartPanel.addChild(this.chartGrid);
				
				// add panel
				//this.chartDisplayObjects.push(this.dbgText);
				this.chartDisplayObjects.push(this.chartPanel);
				
			}
			
			if (this.chart is com.airliquide.alps.chart.display.Column)
			{
				//Alert.show("still a line");
				
				//var flexLabel:Label = new Label();
				
				// set panel
				if (this.chartPanel == null)
				{
					this.chartPanel = new Panel();
				}
				
				this.chartPanel.percentWidth = 85;
				this.chartPanel.percentHeight = 100;
				this.chartPanel.layout = "vertical";
				this.chartPanel.x = 50;
				this.chartPanel.y =225;
				this.chartPanel.setStyle("horizontalAlign", "center");
				this.chartPanel.setStyle("verticalAlign","middle");
				this.chartPanel.setStyle("paddingTop",50);
				this.chartPanel.removeAllChildren();
				
				if (this.Renderer == ALChart.FLEX)
				{
					// set axis category
					this.chartPanel.title = "Air Liquide Charting: HTML Column Chart:FLEX ";
					var flexColumnChart:Column = this.chart as com.airliquide.alps.chart.display.Column;
					
					//Alert.show("using Flex");
					try
					{
						
						
						
						ca = new mx.charts.CategoryAxis();
						ca.categoryField = this.GetYLabel();
						flexColumnChart.SetXAxisCategory(ca);
						
						if (this.dataParser != null)
						{
							// set up the series information
							flexColumnChart.SetSeries(this.dataParser.ParseCurrentData(FormatParser.FROM_CURRENT_DATA_TO_COLUMNSERIES_ARRAY,this.GetYLabel(),this.GetXLabel()) as Array);
							//this.dbgText.text += this.dataParser.GetDebugText();
						}
						
						
						var cSeries:ColumnSeries = new ColumnSeries();
						//lSeries.xField = "Score For";
						cSeries.yField = "Score";
						//lSeries.displayName = "Score For";
						sArray = new Array();
						sArray.push(cSeries);
						flexColumnChart.SetSeries(sArray);
						
						
						// initialize & draw the chart
						flexColumnChart.DrawColumnChart();
						
						
						var cc:ChartBase = flexColumnChart.GetFlexChart();
						cc.percentHeight = 75;
						cc.percentWidth = 75;
						cc.showDataTips = true;
						
						// add chart
						this.chartPanel.addChild(cc);
						
					
					}
					catch (err:Error)
					{
						Alert.show("[ERROR] problems redinring col chart in FLEX:"+err.message);
					}
				}
				
				if (this.Renderer == ALChart.AM)
				{
					this.chartPanel.title = "Air Liquide Charting: HTML Column Chart:AM";
					var amColumnChart:com.airliquide.alps.chart.display.Column = this.chart as com.airliquide.alps.chart.display.Column;
					var cg:AmGraph = new AmGraph();
					var cgs:Array = new Array();
					var cgsv:Array = new Array();
					
					var aggrkey:KpiLegend = new KpiLegend();
					
					
					var clgs:Array = new Array();
					var lgsv:Array = new Array();
					var lgsd:ArrayCollection = new ArrayCollection();
					var cgsd:ArrayCollection = new ArrayCollection();
					var cd_raw:Dictionary = new Dictionary();
					
					f = 0;
					var cava:ValueAxis = new com.amcharts.axes.ValueAxis();
					
					cava.setStyle("axisThickness", 2);
					cava.stackType = "regular";
					var ccolumnac:ArrayCollection = amColumnChart.GetArrayCollectionData();
					var comboNum:Number = ccolumnac.length;
					var data:Dictionary; 
					kpiCounters = new Dictionary();
					
					
					if (amColumnChart.GetTotalDateAndValueCollectionData() != null)
					{
						//Alert.show("total");
						if (amColumnChart.GetArrayCollectionData() != null)
						{
							if (this.ChartArrayValues)
							{
								comboNum = this.GetKPISetTotal();
							}
							//Alert.show("collection data length="+amBarChart.GetTotalDateAndValueCollectionData().length);
							//Alert.show("total & aggre");
							for (f=0; f < comboNum; f++ )
							{
								cava= new com.amcharts.axes.ValueAxis();
								cava.setStyle("axisThickness", 2);
								cava.stackType = "regular";

								//Alert.show("f num:"+f.toString()+":ccolumn.length="+ccolumnac.length);
								cg = new AmGraph();
								cg.valueField = f.toString();
								cg.type = "column";
								//cg.title = "Scores"
								//cg.negativeBase = new Number(009900);
								//cg.fillColorsField = "#CC0000";
								cg.setStyle("fillAlphas", [0.75]);
								//cg.setStyle("lineColor", "#FF0000");
								
								if (this.ChartArrayValues)
								{
									data = this.GetKPISetValues()[f]; 
									if (data != null)
									{
										this.setAmChartKpiLabelLineColor(data["KPI_Name"], cg, false);
									}
									else
									{
										//cg.title = "scores-";
									}
								}
								else
								{
									data = ccolumnac[f];
									if (data != null)
									{
										cg.title = data[this.GetYLabel()] + " Aggregate Score";
										cg.balloonText = "Actual "+ this.GetKPIName() +" Score is [[value]]";
										this.setAmChartKpiLabelLineColor(this.GetKPIName(), cg, true);
									}
									
								}

								cg.addEventListener(GraphEvent.ROLL_OVER_GRAPH_ITEM, aggrkey.HandleChartMouseOver);
								cg.addEventListener(GraphEvent.CLICK_GRAPH_ITEM, aggrkey.HandleChartClick);
								cg.addEventListener(GraphEvent.ROLL_OUT_GRAPH_ITEM, aggrkey.HandleChartMouseOut);
							
								cgs.push(cg);

								cgsv.push(cava);
							}
							
							cg = new AmGraph();
							cg.valueField = "max";
							cg.type = "column";
							//cg.negativeBase = new Number(990000);
							//cg.colorField = "#FF0000";
							//cg.fillColorsField = ["#0000FF"];
							//var fcs:Array = new Array();
							//fcs.push("#00FF00");
							cg.setStyle("lineColor","#00ABC0");
							//cg.setStyle("fillColors","[#0000FF]");
							cg.setStyle("fillAlphas", [0.10]);
							//cg.colorField = "[#FF0000]";
							
							if (this.ChartArrayValues)
							{
								//data = this.chart.GetArrayCollectionData()[0];
								cg.balloonText = "Maximum Score Possible for KPI(s) Selected Includes [[value]] Additional Points.";
								cg.title = "Max Possible Score";
							}
							else
							{
								cg.balloonText = "Maximum Score Possible for KPI(s) Selected Includes [[value]] Additional Points.";
								cg.title = this.GetKPIName() + " Maximum Possible Score ";
							}
							
							cgs.push(cg);
							
							cava = new com.amcharts.axes.ValueAxis();
							cava.setStyle("axisThickness", 2);
							cava.stackType = "regular";
							cava.setStyle("gridAlpha","0.1");
				
							cgsv.push(cava);
						}
						
						var ctestac:ArrayCollection = amColumnChart.GetTotalDateAndValueCollectionData();
						
						ava_raw = new com.amcharts.axes.ValueAxis();
						ava_raw.setStyle("axisThickness", 2);
						ava_raw.min = 0;
						ava_raw.max = 100;
						lgsv = new Array();
						lgsv.push(ava_raw);
						
						for (c=0; c < ctestac.length; c++ )
						{
							var ctest2ac:ArrayCollection = ctestac.getItemAt(c).coll as ArrayCollection;
							if (ctest2ac != null)
							{
								for (d = 0; d < ctest2ac.length; d++ )
								{
									cd_raw = new Dictionary();
									cd_raw["score_" + c] = ctest2ac.getItemAt(d).score;
									cd_raw["date"] = ctest2ac.getItemAt(d).date;
									lgsd.addItem(cd_raw);
									//Alert.show("Date=" + d_raw["date"] + "&Score" + test2ac.getItemAt(d).score);
									//Alert.show("*")
								}
								//Alert.show("we are here:c="+c);
								//Alert.show("Another Axis!");
								if (c > 0)
								{
									
									ava_raw = new com.amcharts.axes.ValueAxis();
									ava_raw.setStyle("axisThickness", 2);
									ava_raw.setStyle("gridAlpha", 0);
									//ava_raw.offset = 50;
									ava_raw.min = 0;
									ava_raw.max = 100;
						
								}
								
								lgr = new AmGraph();
								lgr.valueField = "score_" + c;
								lgr.type = "smoothedLine";
								
								if (this.ChartArrayValues)
								{
									this.setAmChartArrayKpiLabelSymbolColor(ctestac.getItemAt(c).kpi, ctestac.getItemAt(c).cat,kpiCounters, lgr, false);
								}
								else
								{
									this.setAmChartArrayKpiLabelSymbolColor(this.GetKPIName(), this.GetKPICategory()[c],kpiCounters, lgr, true);
								}
								
								
								//lgr.setStyle("bullet","round");
								
								lgr.valueAxis = ava_raw;
								
								clgs.push(lgr);
								
								if (c > 0)
								{
									lgsv.push(ava_raw);
								}
								
							}
						}
						
					}
					else
					{
						if (amColumnChart.GetArrayCollectionData() != null)
						{
							//Alert.show("aggre & no total");
							if (ccolumnac.length > 0)
							{
								if (ccolumnac[0]["0"] == null)
								{
									//Alert.show("yo");
									cg.valueField = this.GetXLabel();
									cg.type = "column";
									cg.negativeBase = new Number(009900);
									//cg.fillColorsField = "#CC0000";
									
									this.setAmChartKpiLabelLineColor(this.GetKPIName(), cg, true);
									
									cg.setStyle("fillAlphas", [0.75]);
									cg.title = this.GetKPIName();
									cg.balloonText = "Actual KPI Score [[value]]";
									cgs.push(cg);
									
									cg = new AmGraph();
									cg.valueField = "max";
									cg.type = "column";
									cg.negativeBase = new Number(009900);
									//cg.fillColorsField = "#0000DD";
									cg.setStyle("lineColor","#00ABC0");
									cg.setStyle("fillAlphas", [0.10]);
									cg.title = "Max Includes ";
									cg.balloonText = "Maximum Score Possible for KPI Selected Includes [[value]] Additional Points";
									cgs.push(cg);
									
									cava = new com.amcharts.axes.ValueAxis();
									cava.setStyle("axisThickness", 2);
									cava.stackType = "regular";
									cava.setStyle("gridAlpha","0.1");
						
									cgsv.push(cava);
								}
								else
								{
									//Alert.show("no yo");
									
									if (this.ChartArrayValues)
									{
										//Alert.show("chartArray Vals!!");
										for (var g:Number=0; g < this.GetKPISetTotal(); g++ )
										{
											//Alert.show("g on render="+g+" and tital KPI is="+this.GetKPISetTotal());
											cg = new AmGraph();
											cg.valueField = g.toString();
											cg.type = "column";
											cg.title = "Score For " + this.GetKPISetValues()[g]["KPI_Name"] as String;
											cg.balloonText = (this.GetKPISetValues()[g]["KPI_Name"] as String) + " Score is [[value]]";
											cg.negativeBase = new Number(009900);
											//cg.fillColorsField = "#CC0000";
											this.setAmChartKpiLabelLineColor(this.GetKPISetValues()[g]["KPI_Name"], cg, true);
											
											cg.setStyle("fillAlphas",[0.75]);
											
											cgs.push(cg);
											
											cava = new com.amcharts.axes.ValueAxis();
											cava.setStyle("axisThickness", 2);
											cava.stackType = "regular";
								
											cgsv.push(cava);
											
										}
									}
									else
									{
										//Alert.show("collection data length="+amBarChart.GetTotalDateAndValueCollectionData().length);
										//Alert.show("! chart Array Vals");
										for (f=0; f < ccolumnac.length; f++ )
										{
											cava= new com.amcharts.axes.ValueAxis();
											cava.setStyle("axisThickness", 2);
											cava.stackType = "regular";

											//Alert.show("e num:"+e.toString());
											cg = new AmGraph();
											cg.valueField = f.toString();
											cg.type = "column";
											cg.title = "Score For " + this.GetKPIName() ;
											cg.negativeBase = new Number(009900);
											//cg.fillColorsField = "#CC0000";
											cg.setStyle("fillAlphas", [0.75]);
											
											this.setAmChartKpiLabelLineColor(this.GetKPIName(), cg, true);
											
											//bg.valueAxis = bava;

											cgs.push(cg);

											cgsv.push(cava);
										}
									}
									
									cg = new AmGraph();
									cg.valueField = "max";
									cg.type = "column";
									cg.negativeBase = new Number(009900);
									//cg.fillColorsField = "#0000DD";
									cg.setStyle("lineColor","00ABC0");
									cg.setStyle("fillAlphas", [0.10]);
									cg.title = "Maximum Score Possible for kpi(s) Includes ";
									cg.balloonText = "Maximum Score Possible for KPI Selected Includes [[value]] Additional Points";
									cgs.push(cg);
									
									cava = new com.amcharts.axes.ValueAxis();
									cava.setStyle("axisThickness", 2);
									cava.stackType = "regular";
									cava.setStyle("gridAlpha","0.1");
						
									cgsv.push(cava);
								}
							}
							else
							{
								//Alert.show("this would be what else");
							}
						}
					}
					
					asc = new AmSerialChart();
					//asc.percentWidth = 75;
					asc.width = 750;
					asc.percentHeight = 100;
					asc.dataProvider = amColumnChart.GetArrayCollectionData();
					asc.categoryField = this.GetYLabel();
					asc.graphs = cgs;
					
					asc.categoryAxis = aca;
					asc.valueAxes = cgsv;
					
					//var ccur:ChartCursor = new ChartCursor();
					ccur.useHandCursor = true;
					
					asc.chartCursor = ccur;
					if (this.ThreeDView)
					{
						asc.setStyle("angle", "45");
						asc.setStyle("depth3D", "25");
					}
					asc.setStyle("plotAreaFillClors",[[0xFAFAFA]]);
					
					// add keys for everything
					//var aggrkey:AmLegend = new AmLegend();
					//aggrkey.dataProvider = asc;
					//aggrkey.percentWidth = 35;
					//aggrkey.textClickEnabled = false;
					//aggrkey.setStyle("marginRight", "5");
					//aggrkey.setStyle("marginLeft", "5");
					//aggrkey.setStyle("marginBottom","5");
					aggrkey.percentWidth = 40;
					aggrkey.setStyle("marginRight", "10");
					aggrkey.SetDataProvider(asc.graphs, "GraphArray");		
					
					
					var aggrvb:HBox = new HBox();
					aggrvb.percentHeight = 75;
					aggrvb.addChild(aggrkey);
					aggrvb.addChild(asc);
					
					this.chartPanel.addChild(aggrvb);
							
					// add chart
					//this.chartPanel.addChild(asc);
					
					/***************************************/
					if (amColumnChart.GetTotalDateAndValueCollectionData() != null)
					{
						// set category axis
						var caca_raw:com.amcharts.axes.CategoryAxis = new com.amcharts.axes.CategoryAxis();
						caca_raw.parseDates = true;
						caca_raw.minPeriod = "DD";
						caca_raw.setStyle("gridCount",13);
						
						// set chart cursor
						var cacc_raw:ChartCursor= new ChartCursor();
						cacc_raw.cursorPosition= "mouse";
						
						// set chart cursor
						var cacsb_raw:ChartScrollbar= new ChartScrollbar();
						cacsb_raw.height = 20;
						
						try
						{
							asc_raw = new AmSerialChart();
							asc_raw.percentWidth = 85;
							//asc_raw.percentHeight = 75;
							//asc_raw.height = 300;
							//asc_raw.width = 700;
							asc_raw.dataProvider = lgsd;
							asc_raw.categoryField = "date";
							
							asc_raw.valueAxes = lgsv;
							//asc_raw.addValueAxis(lgsv[0]);
							asc_raw.categoryAxis = caca_raw;
							asc_raw.chartCursor = cacc_raw;
							asc_raw.chartScrollbar = cacsb_raw;
							asc_raw.graphs = clgs;
							
							//asc_raw.setStyle("marginTop",15);
							
						}
						catch (er:Error)
						{
							Alert.show("we have problems in col chart:"+er.message+ "#"+er.getStackTrace());
						}
						
						var vb:HBox = new HBox();
						/*
						var key:AmLegend = new AmLegend();
						key.dataProvider = asc_raw;
						key.percentWidth = 25;
						key.textClickEnabled = false;
						key.setStyle("marginRight", "5");
						key.setStyle("marginLeft", "10");
						*/
						var key:KpiLegend = new KpiLegend();
					
						if (this.ChartArrayValues)
						{
							vb.percentHeight = 55;
							vb.addChild(key);
							key.SetDataProvider(this.GetKPISetValues(),"Array");
							vb.addChild(asc_raw);
							
							if (amColumnChart.GetTotalDateAndValueCollectionData().length > 0)
							{
								this.chartPanel.addChild(vb);
							}
							//this.chartPanel.addChild(asc_raw);
							
						}
						else
						{
							this.chartPanel.addChild(asc_raw);
							//vb.percentHeight = 75;
							//vb.addChild(key);
							//vb.addChild(asc_raw);
							//this.chartPanel.addChild(vb);
							//Alert.show("Yo-yo");
						}
					}
				}
				
				
				this.chartGrid = new DataGrid();
				
				this.chartGrid.dataProvider = chartDataArray;
				this.chartGrid.percentHeight = 40;
				this.chartGrid.percentWidth = 100;
				this.chartGrid.allowMultipleSelection = true;
				
				// add chart
				//this.chartPanel.addChild(this.chartGrid);
				
				// add panel
				//this.chartDisplayObjects.push(this.dbgText);
				this.chartDisplayObjects.push(this.chartPanel);
				
			}
			
			if (this.chart is com.airliquide.alps.chart.display.ColumnArea)
			{
				//Alert.show("still a column area");
				
				// set panel
				if (this.chartPanel == null)
				{
					this.chartPanel = new Panel();
				}
				
				this.chartPanel.title = "Air Liquide Charting: HTML Column & Area Chart:FLEX ";
				this.chartPanel.percentWidth = 85;
				this.chartPanel.percentHeight = 85;
				this.chartPanel.layout = "vertical";
				this.chartPanel.x = 50;
				this.chartPanel.y = 200;
				this.chartPanel.setStyle("horizontalAlign", "center");
				this.chartPanel.setStyle("verticalAlign","middle");
				this.chartPanel.setStyle("paddingTop",50);
				this.chartPanel.removeAllChildren();
			
				if (this.Renderer == ALChart.FLEX)
				{
					this.chartPanel.title = "Air Liquide Charting: HTML Column & Area Chart:FLEX ";
					var flexColumnAreaChart:ColumnArea = this.chart as com.airliquide.alps.chart.display.ColumnArea;
					
					
					try
					{
						//column first
						ca = new mx.charts.CategoryAxis();
						ca.categoryField = this.GetYLabel();
						flexColumnAreaChart.SetXAxisCategory(ca);
						
						if (this.dataParser != null)
						{
							flexColumnAreaChart.SetColumnSeries(this.dataParser.ParseCurrentData(FormatParser.FROM_CURRENT_DATA_TO_COLUMNSERIES_ARRAY,this.GetYLabel(),this.GetXLabel()) as Array);
						}
						
						
						var caSeries:ColumnSeries = new ColumnSeries();
						caSeries.yField = "Score";
						sArray = new Array();
						sArray.push(caSeries);
						flexColumnAreaChart.SetColumnSeries(sArray);
						
						// initialize & draw the chart
						flexColumnAreaChart.DrawColumnAreaChart();
						
						var cca:ChartBase = flexColumnAreaChart.GetFlexColumnChart();
						cca.percentHeight = 75;
						cca.percentWidth = 75;
						cca.showDataTips = true;
						
						// add chart
						this.chartPanel.addChild(cca);
						
						// then area
						
						
						ca = new mx.charts.CategoryAxis();
						ca.categoryField = this.GetYLabel();
						flexColumnAreaChart.SetXAxisCategory(ca);
						
						if (this.dataParser != null)
						{
							// set up the series information
							flexColumnAreaChart.SetAreaSeries(this.dataParser.ParseCurrentData(FormatParser.FROM_CURRENT_DATA_TO_AREASERIES_ARRAY,this.GetYLabel(),this.GetXLabel()) as Array);
							//this.dbgText.text += this.dataParser.GetDebugText();
						}
						
						
						var aSeries:AreaSeries = new AreaSeries();
						//lSeries.xField = "Score For";
						aSeries.yField = "Score";
						//lSeries.displayName = "Score For";
						sArray = new Array();
						sArray.push(aSeries);
						flexColumnAreaChart.SetAreaSeries(sArray);
						
						// initialize & draw the chart
						flexColumnAreaChart.DrawColumnAreaChart();
						
						
						var ac:ChartBase = flexColumnAreaChart.GetFlexAreaChart();
						ac.percentHeight = 75;
						ac.percentWidth = 75;
						ac.showDataTips = true;
						
						// add chart
						this.chartPanel.addChild(ac);
						
					}
					catch (err:Error)
					{
						Alert.show("[ERROR] problems displaying Column Area chart using FLEX:"+err.getStackTrace());
					}
				}
				
				if (this.Renderer == ALChart.AM)
				{
					this.chartPanel.title = "Air Liquide Charting: HTML Column & Area Chart:AM ";
					var amColumnAreaChart:ColumnArea = this.chart as com.airliquide.alps.chart.display.ColumnArea;
					
					try
					{
						var cacg:AmGraph = new AmGraph();
						cacg.valueField = this.GetXLabel();
						cacg.type = "column";
						//cacg.negativeBase = new Number(009900);
						//cacg.fillColorsField = "#FF0000";
						cacg.setStyle("fillAlphas",[0.3]);
					
						var caag:AmGraph = new AmGraph();
						caag.valueField = this.GetXLabel();
						caag.type = "line";
						//caag.negativeBase = new Number(009900);
						//caag.fillColorsField = "#0000FF";
						caag.setStyle("fillAlphas",[0.3]);
					
						//this.dbgText.text += flexBarChart.dbgText.text;
						var cags:Array = new Array();
						cags.push(cacg);
						cags.push(caag);
					
						asc = new AmSerialChart();
						asc.percentWidth = 75;
						asc.percentHeight = 75;
						asc.dataProvider = amColumnAreaChart.GetArrayCollectionData();
						
						asc.categoryField = this.GetYLabel();
						asc.graphs = cags;
					
					
						asc.categoryAxis = aca;
					
						//var ccur:ChartCursor = new ChartCursor();
						ccur.useHandCursor = true;
					
						asc.chartCursor = ccur;
						if (this.ThreeDView)
						{
							asc.setStyle("angle", "30");
							asc.setStyle("depth3D", "20");
						}
						
						asc.setStyle("plotAreaFillAlphas",[0]);
					
						// add chart
						this.chartPanel.addChild(asc);
						
						
					
					}
					catch (err:Error)
					{
						
					}
				}
				
				// add panel
				//this.chartDisplayObjects.push(this.dbgText);
				this.chartDisplayObjects.push(this.chartPanel);
			}
			
		}
		
		public function Create():void
		{
			//dbgText.text += "in create chart public:";
			
			{
				this.createChart();
			}
		}
		
		public function GetChartDisplayObjects():Array
		{
			return this.chartDisplayObjects;
		}
		
		public function LoadChartDataValues():void 
		{
			var b:Bar = null;
			var c:Column = null;
			var l:Line = null;
			var ca:ColumnArea = null;
			
			if (this.Renderer == ALChart.FLEX)
			{
				//this.dbgText.text += "Loading chart FLEX:";
				
				if (this.DisplayFormat == ALChart.BAR)
				{
					this.loadFlexBarWithData(b);
				}
				
				if (this.DisplayFormat == ALChart.LINE)
				{
					this.loadFlexLineWithData(l);
				}
				
				if (this.DisplayFormat == ALChart.COLUMN)
				{
					this.loadFlexColumnWithData(c);
				}
				
				if (this.DisplayFormat == ALChart.COLUMN_AREA)
				{
					this.loadFlexColumnAreaWithData(ca);
				}
			}
			
			if (this.Renderer == ALChart.AM)
			{
				//this.dbgText.text += "Loading chart AM:";
				
				if (this.DisplayFormat == ALChart.BAR)
				{
					this.loadAmBarWithData(b);
				}
				
				if (this.DisplayFormat == ALChart.LINE)
				{
					this.loadAmLineWithData(l);
				}
				
				if (this.DisplayFormat == ALChart.COLUMN)
				{
					this.loadAmColumnWithData(c);
				}
				
				if (this.DisplayFormat == ALChart.COLUMN_AREA)
				{
					//Alert.show("loading Flex column area w/ data");
					this.loadAmColumnAreaWithData(ca);
				}
			}
		}
		
		private function setRelevantColors():void
		{
			sc= new SolidColor(0xCCCCCC, .6);
			this.chartColors.push(sc);
			sc= new SolidColor(0x00CCCC, .8);
			this.chartColors.push(sc);
			sc= new SolidColor(0x00FF00, .6);
			this.chartColors.push(sc);
		}
		
		private function setRelevantStrokes():void
		{
			stroke = new Stroke(0xFF0000, 2);
			this.chartStrokes.push(stroke);
			stroke = new Stroke(0x0000FF, 2);
			this.chartStrokes.push(stroke);
			stroke = new Stroke(0x00FF00, 2);
			this.chartStrokes.push(stroke);
		}
		
		private function createChart():void
		{
			//dbgText.text += "in create chart private:";
			switch(this.DisplayFormat)
			{
				case ALChart.BAR:
					//Alert.show("It is also a BAR");
					this.chart = new com.airliquide.alps.chart.display.Bar() as com.airliquide.alps.chart.display.Bar;
					this.chart.SetRenderrer(this.Renderer);
					//dbgText.text += this.Renderer + ":";
					
					//this.chart.
					break;
				case ALChart.LINE:
					//Alert.show("It is also a LINE");
					this.chart = new com.airliquide.alps.chart.display.Line() as com.airliquide.alps.chart.display.Line;
					this.chart.SetRenderrer(this.Renderer);
					//dbgText.text += this.Renderer + ":";
					break;
				case ALChart.COLUMN:
					//Alert.show("It is also a COLUMN");
					this.chart = new com.airliquide.alps.chart.display.Column() as com.airliquide.alps.chart.display.Column;
					this.chart.SetRenderrer(this.Renderer);
					//dbgText.text += this.Renderer + ":";
					break;
				case ALChart.COLUMN_AREA:
					//Alert.show("It is also a COLUMN_AREA");
					this.chart = new com.airliquide.alps.chart.display.ColumnArea() as com.airliquide.alps.chart.display.ColumnArea;
					this.chart.SetRenderrer(this.Renderer);
					//dbgText.text += this.Renderer + ":";
					break;
			}
		}
		
		private function loadFlexColumnWithData(c:Column):void
		{
			//Alert.show("definately a line");
			// type cast to the right display type
			if (c == null)
			{
				this.createChart();
				//Alert.show("created a chart");
			}
			c  = this.chart as Column;
			
			//this.dbgText.text += "before piped values :";
			
			if (this.PipedValues)
			{
				if (this.DictArrayValues == false && this.DictValues == false )
				{
					// go from string to arrayCollection for chart
					c.SetArrayCollectionData(this.dataParser.ParseDataString(this.GetData(),FormatParser.FROM_COMMA_AND_PIPE_TO_X_AND_Y_ARRAY_COLLECTION,this.GetXLabel(),this.GetYLabel()) as ArrayCollection);
					
				}
				else
				{
					if (this.DictArrayValues)
					{
						this.dataParser.SetAggregationData(this.GetAggregationData());
						c.SetArrayCollectionData(this.dataParser.ParseDataString("AggregatedArray",FormatParser.FROM_COMMA_AND_PIPE_TO_X_AND_Y_ARRAY_COLLECTION,this.GetXLabel(),this.GetYLabel()) as ArrayCollection);
					}
					
					if (this.DictValues)
					{
						
					}
				}
				
				//this.dbgText.text += "piped"+this.dataParser.GetDebugText();
			}
			else
			{
				if (this.DictArrayValues == false && this.DictValues == false )
				{
					if (c != null)
					{
						c.SetArrayCollectionData(this.dataParser.ParseDataString(this.GetData(),FormatParser.FROM_COMMA_DATE_METRIC_TO_X_AND_Y_ARRAY_COLLECTION,this.GetXLabel(),this.GetYLabel()) as ArrayCollection);
					}
					
					//this.dbgText.text += "regular data*"+this.dataParser.GetDebugText();
				}
				else
				{
					//this.dbgText.text += "both not false";
					if (this.DictArrayValues)
					{
						this.dataParser.SetAggregationData(this.GetAggregationData());
						c.SetArrayCollectionData(this.dataParser.ParseDataString("AggregatedArray",FormatParser.FROM_COMMA_DATE_METRIC_TO_X_AND_Y_ARRAY_COLLECTION,this.GetXLabel(),this.GetYLabel()) as ArrayCollection);
						//this.dbgText.text = "aggregated data*"+this.dataParser.GetDebugText();
						//Alert.show("doing the aggregated array")
						//this.dataParser.SetTotalDataSet(this.GetCategoryAxisDates(),this.GetCategoryAxisValues());
						//c.SetTotalDataDatesAndValues(this.dataParser.ParseDataString("TotalDataArray", FormatParser.FROM_COMMA_DATE_METRIC_TO_ARRAY_COLLECTION, "Dates", "Scores") as ArrayCollection );
					}
				}
				
				//this.dbgText.text += "notpiped"+this.dataParser.GetDebugText();
			}
			
			if (this.dataParser == null)
			{
				Alert.show("null parser");
			}
			else
			{
				//Alert.show("parser is an object");
			}
			
			// then to dioctionaty for grid
			this.chartDataArray = this.dataParser.ParseCurrentData(FormatParser.FROM_CURRENT_DATA_TO_ARRAY_COLLECTION,this.GetXLabel(),this.GetYLabel()) as ArrayCollection;
		
		}
		
		private function loadFlexLineWithData(l:Line ):void
		{
			//Alert.show("definately a line");
			// type cast to the right display type
			if (l == null)
			{
				this.createChart();
				//Alert.show("created a chart");
			}
			l  = this.chart as Line;
			
			//this.dbgText.text += "before piped values :";
			
			if (this.PipedValues)
			{
				if (this.DictArrayValues == false && this.DictValues == false )
				{
					// go from string to arrayCollection for chart
					l.SetArrayCollectionData(this.dataParser.ParseDataString(this.GetData(),FormatParser.FROM_COMMA_AND_PIPE_TO_X_AND_Y_ARRAY_COLLECTION,this.GetXLabel(),this.GetYLabel()) as ArrayCollection);
					
				}
				else
				{
					if (this.DictArrayValues)
					{
						this.dataParser.SetAggregationData(this.GetAggregationData());
						l.SetArrayCollectionData(this.dataParser.ParseDataString("AggregatedArray",FormatParser.FROM_COMMA_AND_PIPE_TO_X_AND_Y_ARRAY_COLLECTION,this.GetXLabel(),this.GetYLabel()) as ArrayCollection);
					}
					
					if (this.DictValues)
					{
						
					}
				}
				
				//this.dbgText.text += "piped"+this.dataParser.GetDebugText();
			}
			else
			{
				if (this.DictArrayValues == false && this.DictValues == false )
				{
					if (l != null)
					{
						l.SetArrayCollectionData(this.dataParser.ParseDataString(this.GetData(),FormatParser.FROM_COMMA_DATE_METRIC_TO_X_AND_Y_ARRAY_COLLECTION,this.GetXLabel(),this.GetYLabel()) as ArrayCollection);
					}
					
					this.dbgText.text += "regular data*"+this.dataParser.GetDebugText();
				}
				else
				{
					//this.dbgText.text += "both not false";
					if (this.DictArrayValues)
					{
						this.dataParser.SetAggregationData(this.GetAggregationData());
						l.SetArrayCollectionData(this.dataParser.ParseDataString("AggregatedArray",FormatParser.FROM_COMMA_DATE_METRIC_TO_X_AND_Y_ARRAY_COLLECTION,this.GetXLabel(),this.GetYLabel()) as ArrayCollection);
						//this.dbgText.text = "aggregated data*"+this.dataParser.GetDebugText();
					}
					
					if (this.DictValues)
					{
						
					}
				}
				
				//this.dbgText.text += "notpiped"+this.dataParser.GetDebugText();
			}
			
			if (this.dataParser == null)
			{
				Alert.show("null parser");
			}
			else
			{
				//Alert.show("parser is an object");
			}
			
			// then to dioctionaty for grid
			this.chartDataArray = this.dataParser.ParseCurrentData(FormatParser.FROM_CURRENT_DATA_TO_ARRAY_COLLECTION,this.GetXLabel(),this.GetYLabel()) as ArrayCollection;
		
		}
		private function loadFlexBarWithData(b:Bar):void
		{
			//this.dbgText.text += "Loading chart FLEX BAR :";
			if (b == null)
			{
				this.createChart();
			}
			
			// type cast to the right display type
			b = this.chart as Bar;
			
			//this.dbgText.text += "before piped values :";
			
			if (this.PipedValues)
			{
				if (this.DictArrayValues == false && this.DictValues == false )
				{
					// go from string to arrayCollection for chart
					b.SetArrayCollectionData(this.dataParser.ParseDataString(this.GetData(),FormatParser.FROM_COMMA_AND_PIPE_TO_X_AND_Y_ARRAY_COLLECTION,this.GetXLabel(),this.GetYLabel()) as ArrayCollection);
					
				}
				else
				{
					if (this.DictArrayValues)
					{
						this.dataParser.SetAggregationData(this.GetAggregationData());
						b.SetArrayCollectionData(this.dataParser.ParseDataString("AggregatedArray",FormatParser.FROM_COMMA_AND_PIPE_TO_X_AND_Y_ARRAY_COLLECTION,this.GetXLabel(),this.GetYLabel()) as ArrayCollection);
					}
					
					if (this.DictValues)
					{
						
					}
				}
				
				//this.dbgText.text += "piped"+this.dataParser.GetDebugText();
			}
			else
			{
				if (this.DictArrayValues == false && this.DictValues == false )
				{
					b.SetArrayCollectionData(this.dataParser.ParseDataString(this.GetData(),FormatParser.FROM_COMMA_DATE_METRIC_TO_X_AND_Y_ARRAY_COLLECTION,this.GetXLabel(),this.GetYLabel()) as ArrayCollection);
					//this.dbgText.text += "regular data*"+this.dataParser.GetDebugText();
				}
				else
				{
					//this.dbgText.text += "both not false";
					if (this.DictArrayValues)
					{
						this.dataParser.SetAggregationData(this.GetAggregationData());
						b.SetArrayCollectionData(this.dataParser.ParseDataString("AggregatedArray",FormatParser.FROM_COMMA_DATE_METRIC_TO_X_AND_Y_ARRAY_COLLECTION,this.GetXLabel(),this.GetYLabel()) as ArrayCollection);
						//this.dbgText.text = "aggregated data*"+this.dataParser.GetDebugText();
					}
					
					if (this.DictValues)
					{
						
					}
				}
				
				//this.dbgText.text += "notpiped"+this.dataParser.GetDebugText();
			}
			
			// then to dioctionaty for grid
			this.chartDataArray = this.dataParser.ParseCurrentData(FormatParser.FROM_CURRENT_DATA_TO_ARRAY_COLLECTION,this.GetXLabel(),this.GetYLabel()) as ArrayCollection;
		
		}
		
		private function loadFlexColumnAreaWithData(ca:ColumnArea):void
		{
			//Alert.show("definately a line");
			// type cast to the right display type
			if (ca == null)
			{
				this.createChart();
				//Alert.show("created a chart");
			}
			ca  = this.chart as ColumnArea;
			
			//this.dbgText.text += "before piped values :";
			
			if (this.PipedValues)
			{
				if (this.DictArrayValues == false && this.DictValues == false )
				{
					// go from string to arrayCollection for chart
					ca.SetArrayCollectionData(this.dataParser.ParseDataString(this.GetData(),FormatParser.FROM_COMMA_AND_PIPE_TO_X_AND_Y_ARRAY_COLLECTION,this.GetXLabel(),this.GetYLabel()) as ArrayCollection);
					
				}
				else
				{
					if (this.DictArrayValues)
					{
						this.dataParser.SetAggregationData(this.GetAggregationData());
						ca.SetArrayCollectionData(this.dataParser.ParseDataString("AggregatedArray",FormatParser.FROM_COMMA_AND_PIPE_TO_X_AND_Y_ARRAY_COLLECTION,this.GetXLabel(),this.GetYLabel()) as ArrayCollection);
					}
					
					if (this.DictValues)
					{
						
					}
				}
				
				//this.dbgText.text += "piped"+this.dataParser.GetDebugText();
			}
			else
			{
				if (this.DictArrayValues == false && this.DictValues == false )
				{
					if (ca != null)
					{
						ca.SetArrayCollectionData(this.dataParser.ParseDataString(this.GetData(),FormatParser.FROM_COMMA_DATE_METRIC_TO_X_AND_Y_ARRAY_COLLECTION,this.GetXLabel(),this.GetYLabel()) as ArrayCollection);
					}
					
					//this.dbgText.text += "regular data*"+this.dataParser.GetDebugText();
				}
				else
				{
					//this.dbgText.text += "both not false";
					if (this.DictArrayValues)
					{
						this.dataParser.SetAggregationData(this.GetAggregationData());
						ca.SetArrayCollectionData(this.dataParser.ParseDataString("AggregatedArray",FormatParser.FROM_COMMA_DATE_METRIC_TO_X_AND_Y_ARRAY_COLLECTION,this.GetXLabel(),this.GetYLabel()) as ArrayCollection);
						//this.dbgText.text = "aggregated data*"+this.dataParser.GetDebugText();
					}
					
					if (this.DictValues)
					{
						
					}
				}
				
				//this.dbgText.text += "notpiped"+this.dataParser.GetDebugText();
			}
			
			if (this.dataParser == null)
			{
				Alert.show("null parser");
			}
			else
			{
				//Alert.show("parser is an object");
			}
			
			// then to dioctionaty for grid
			this.chartDataArray = this.dataParser.ParseCurrentData(FormatParser.FROM_CURRENT_DATA_TO_ARRAY_COLLECTION,this.GetXLabel(),this.GetYLabel()) as ArrayCollection;
		
		}
		
		private function loadAmBarWithData(b:Bar):void
		{
			//this.dbgText.text += "Loading chart AM BAR :";
			//Alert.show("Loading chart AM BAR");
			
			try
			{
				if (b == null)
				{
					this.createChart();
				}
				
				// type cast to the right display type
				b  = this.chart as Bar;
				
				//this.dbgText.text += "before piped values :";
				
				if (this.PipedValues)
				{
					if (this.DictArrayValues == false && this.DictValues == false )
					{
						// go from string to arrayCollection for chart
						b.SetArrayCollectionData(this.dataParser.ParseDataString(this.GetData(),FormatParser.FROM_COMMA_AND_PIPE_TO_X_AND_Y_ARRAY_COLLECTION,this.GetXLabel(),this.GetYLabel()) as ArrayCollection);
						
					}
					else
					{
						if (this.DictArrayValues)
						{
							this.dataParser.SetAggregationData(this.GetAggregationData());
							b.SetArrayCollectionData(this.dataParser.ParseDataString("AggregatedArray",FormatParser.FROM_COMMA_AND_PIPE_TO_X_AND_Y_ARRAY_COLLECTION,this.GetXLabel(),this.GetYLabel()) as ArrayCollection);
						}
					}
					//this.dbgText.text += "piped"+this.dataParser.GetDebugText();
				}
				
				
				if(!this.PipedValues)
				{
					if (this.DictArrayValues == false && this.DictValues == false && this.ChartArrayValues == false)
					{
						this.dataParser.SetKpiMaxConstants(this.GetKPIMaxData());
						b.SetArrayCollectionData(this.dataParser.ParseDataString(this.GetData(),FormatParser.FROM_COMMA_DATE_METRIC_TO_X_AND_Y_ARRAY_COLLECTION,this.GetXLabel(),this.GetYLabel()) as ArrayCollection);
						//Alert.show("ACData:"+b.GetArrayCollectionData().length);
					}
					
					if(this.DictValues == false)
					{
						//this.dbgText.text += "both not false";
						if (this.DictArrayValues)
						{
							//Alert.show("dictArray Vaklues");
							try
							{
								this.dataParser.SetKpiMaxConstants(this.GetKPIMaxData());
								this.dataParser.SetAggregationData(this.GetAggregationData());

								b.SetArrayCollectionData(this.dataParser.ParseDataString("AggregatedArray",FormatParser.FROM_COMMA_DATE_METRIC_TO_X_AND_Y_ARRAY_COLLECTION,this.GetXLabel(),this.GetYLabel()) as ArrayCollection);
								
								if ((this.GetCategoryAxisValues().toString() != "") && (this.GetCategoryAxisValues().toString() != ","))
								{
									this.dataParser.SetTotalDataSet(this.GetCategoryAxisDates(), this.GetCategoryAxisValues());
									var testAC:ArrayCollection = this.dataParser.ParseDataString("TotalDataArray", FormatParser.FROM_COMMA_DATE_METRIC_TO_ARRAY_COLLECTION, "Dates", "Scores") as ArrayCollection;
									
									//Alert.show("collection has data:"+testAC);
									b.SetTotalDataDatesAndValues( testAC);
								}
							}
							catch (err:Error)
							{
								Alert.show("problems with DictArrayValues:"+err.getStackTrace());
							}
						}
						
						if (this.ChartArrayValues)
						{
							try
							{
								//Alert.show("chart array values!!");
								this.dataParser.SetDataChartArray(this.GetKPISetValues());
								
								// first chart
								b.SetArrayCollectionData(this.dataParser.ParseDataChartArray("AggregatedChart",FormatParser.FROM_COMMA_DATE_METRIC_TO_X_AND_Y_ARRAY_COLLECTION) as ArrayCollection);
								
								// second chart
								b.SetTotalDataDatesAndValues(this.dataParser.ParseDataChartArray("TotalDataChart", FormatParser.FROM_COMMA_DATE_METRIC_TO_ARRAY_COLLECTION) as ArrayCollection);
									
							}
							catch (er:Error)
							{
								Alert.show("problems with ChartArrayValues:"+er.getStackTrace());
							}
						}
						else
						{
							this.dataParser.SetDataChartArray(null);
						}
					}
					
					//this.dbgText.text += "notpiped"+this.dataParser.GetDebugText();
				}
				
				// then to dioctionaty for grid
				this.chartDataArray = this.dataParser.ParseCurrentData(FormatParser.FROM_CURRENT_DATA_TO_ARRAY_COLLECTION,this.GetXLabel(),this.GetYLabel()) as ArrayCollection;
			}
			catch (err:Error)
			{
				Alert.show("[ERROR]problems loading AM Bar chart:"+err.getStackTrace());
			}
		}
		
		private function loadAmLineWithData(l:Line):void
		{
			//this.dbgText.text += "Loading chart AM BAR :";
			//Alert.show("Loading chart AM BAR");
			
			try
			{
				if (l == null)
				{
					this.createChart();
				}
				
				// type cast to the right display type
				l  = this.chart as Line;
				
				//this.dbgText.text += "before piped values :";
				
				if (this.PipedValues)
				{
					if (this.DictArrayValues == false && this.DictValues == false )
					{
						// go from string to arrayCollection for chart
						l.SetArrayCollectionData(this.dataParser.ParseDataString(this.GetData(),FormatParser.FROM_COMMA_AND_PIPE_TO_X_AND_Y_ARRAY_COLLECTION,this.GetXLabel(),this.GetYLabel()) as ArrayCollection);
						
					}
					else
					{
						if (this.DictArrayValues)
						{
							this.dataParser.SetAggregationData(this.GetAggregationData());
							l.SetArrayCollectionData(this.dataParser.ParseDataString("AggregatedArray",FormatParser.FROM_COMMA_AND_PIPE_TO_X_AND_Y_ARRAY_COLLECTION,this.GetXLabel(),this.GetYLabel()) as ArrayCollection);
						}
						
						if (this.DictValues)
						{
							
						}
					}
					
					//this.dbgText.text += "piped"+this.dataParser.GetDebugText();
				}
				else
				{
					if (this.DictArrayValues == false && this.DictValues == false )
					{
						l.SetArrayCollectionData(this.dataParser.ParseDataString(this.GetData(),FormatParser.FROM_COMMA_DATE_METRIC_TO_X_AND_Y_ARRAY_COLLECTION,this.GetXLabel(),this.GetYLabel()) as ArrayCollection);
						//Alert.show("ACData:"+l.GetArrayCollectionData().length);
					}
					else
					{
						//this.dbgText.text += "both not false";
						if (this.DictArrayValues)
						{
							this.dataParser.SetAggregationData(this.GetAggregationData());
							l.SetArrayCollectionData(this.dataParser.ParseDataString("AggregatedArray",FormatParser.FROM_COMMA_DATE_METRIC_TO_X_AND_Y_ARRAY_COLLECTION,this.GetXLabel(),this.GetYLabel()) as ArrayCollection);
							//this.dbgText.text = "aggregated data*"+this.dataParser.GetDebugText();
						}
					}
				}
				
				// then to dioctionaty for grid
				this.chartDataArray = this.dataParser.ParseCurrentData(FormatParser.FROM_CURRENT_DATA_TO_ARRAY_COLLECTION,this.GetXLabel(),this.GetYLabel()) as ArrayCollection;
				}
			catch (err:Error)
			{
				Alert.show("[ERROR]problems loading AM Column chart:"+err.getStackTrace());
			}	
		}
		
		private function loadAmColumnWithData(c:Column):void
		{
			//this.dbgText.text += "Loading chart AM BAR :";
			//Alert.show("Loading chart AM BAR");
			
			try
			{
				if (c == null)
				{
					this.createChart();
				}
				
				// type cast to the right display type
				c  = this.chart as Column;
				
				//this.dbgText.text += "before piped values :";
				
				if (this.PipedValues)
				{
					if (this.DictArrayValues == false && this.DictValues == false )
					{
						// go from string to arrayCollection for chart
						c.SetArrayCollectionData(this.dataParser.ParseDataString(this.GetData(),FormatParser.FROM_COMMA_AND_PIPE_TO_X_AND_Y_ARRAY_COLLECTION,this.GetXLabel(),this.GetYLabel()) as ArrayCollection);
						
					}
					else
					{
						if (this.DictArrayValues)
						{
							this.dataParser.SetAggregationData(this.GetAggregationData());
							c.SetArrayCollectionData(this.dataParser.ParseDataString("AggregatedArray",FormatParser.FROM_COMMA_AND_PIPE_TO_X_AND_Y_ARRAY_COLLECTION,this.GetXLabel(),this.GetYLabel()) as ArrayCollection);
						
							
						}
						
						if (this.DictValues)
						{
							
						}
					}
					
					//this.dbgText.text += "piped"+this.dataParser.GetDebugText();
				}
				
				
				if(!this.PipedValues)
				{
					//Alert.show("CAVs:"+this.ChartArrayValues);
					//if (this.DictArrayValues == false && this.DictValues == false )
					if (this.DictArrayValues == false && this.DictValues == false && this.ChartArrayValues == false)
					{
						this.dataParser.SetKpiMaxConstants(this.GetKPIMaxData());
						c.SetArrayCollectionData(this.dataParser.ParseDataString(this.GetData(),FormatParser.FROM_COMMA_DATE_METRIC_TO_X_AND_Y_ARRAY_COLLECTION,this.GetXLabel(),this.GetYLabel()) as ArrayCollection);
						//Alert.show("ACData:"+c.GetArrayCollectionData().length);
						
					}
					else
					{
						//this.dbgText.text += "both not false";
						if (this.DictArrayValues)
						{
							this.dataParser.SetKpiMaxConstants(this.GetKPIMaxData());
							this.dataParser.SetAggregationData(this.GetAggregationData());
							c.SetArrayCollectionData(this.dataParser.ParseDataString("AggregatedArray",FormatParser.FROM_COMMA_DATE_METRIC_TO_X_AND_Y_ARRAY_COLLECTION,this.GetXLabel(),this.GetYLabel()) as ArrayCollection);
							
							//Alert.show("doing the aggregated array column style")
							if ((this.GetCategoryAxisValues().toString() != "") && (this.GetCategoryAxisValues().toString() != ","))
							{
								this.dataParser.SetTotalDataSet(this.GetCategoryAxisDates(),this.GetCategoryAxisValues());
								var testAC:ArrayCollection = this.dataParser.ParseDataString("TotalDataArray", FormatParser.FROM_COMMA_DATE_METRIC_TO_ARRAY_COLLECTION, "Dates", "Scores") as ArrayCollection;
								
								//Alert.show("collection has data:"+testAC);
								c.SetTotalDataDatesAndValues( testAC);
							}
						}
						
						if (this.ChartArrayValues)
						{
							try
							{
								//Alert.show("chart array values!!");
								this.dataParser.SetDataChartArray(this.GetKPISetValues());
								
								// first chart
								c.SetArrayCollectionData(this.dataParser.ParseDataChartArray("AggregatedChart",FormatParser.FROM_COMMA_DATE_METRIC_TO_X_AND_Y_ARRAY_COLLECTION) as ArrayCollection);
								
								// second chart
								c.SetTotalDataDatesAndValues(this.dataParser.ParseDataChartArray("TotalDataChart", FormatParser.FROM_COMMA_DATE_METRIC_TO_ARRAY_COLLECTION) as ArrayCollection);
								//Alert.show("total chart data len:"+c.GetTotalDateAndValueCollectionData().length);
							}
							catch (er:Error)
							{
								Alert.show("problems with ChartArrayValues:"+er.getStackTrace());
							}
						}
						else
						{
							this.dataParser.SetDataChartArray(null);
						}
					}
				}
				
				// then to dioctionaty for grid
				this.chartDataArray = this.dataParser.ParseCurrentData(FormatParser.FROM_CURRENT_DATA_TO_ARRAY_COLLECTION,this.GetXLabel(),this.GetYLabel()) as ArrayCollection;
				}
			catch (err:Error)
			{
				Alert.show("[ERROR]problems loading AM Column chart:"+err.getStackTrace());
			}
		}
		
		private function loadAmColumnAreaWithData(ca:ColumnArea):void
		{
			try
			{
				if (ca == null)
				{
					this.createChart();
				}
				
				// type cast to the right display type
				ca  = this.chart as ColumnArea;
				
				//this.dbgText.text += "before piped values :";
				
				if (this.PipedValues)
				{
					if (this.DictArrayValues == false && this.DictValues == false )
					{
						// go from string to arrayCollection for chart
						ca.SetArrayCollectionData(this.dataParser.ParseDataString(this.GetData(),FormatParser.FROM_COMMA_AND_PIPE_TO_X_AND_Y_ARRAY_COLLECTION,this.GetXLabel(),this.GetYLabel()) as ArrayCollection);
						
					}
					else
					{
						if (this.DictArrayValues)
						{
							this.dataParser.SetAggregationData(this.GetAggregationData());
							ca.SetArrayCollectionData(this.dataParser.ParseDataString("AggregatedArray",FormatParser.FROM_COMMA_AND_PIPE_TO_X_AND_Y_ARRAY_COLLECTION,this.GetXLabel(),this.GetYLabel()) as ArrayCollection);
						}
						
						if (this.DictValues)
						{
							
						}
					}
					
					//this.dbgText.text += "piped"+this.dataParser.GetDebugText();
				}
				else
				{
					if (this.DictArrayValues == false && this.DictValues == false )
					{
						ca.SetArrayCollectionData(this.dataParser.ParseDataString(this.GetData(),FormatParser.FROM_COMMA_DATE_METRIC_TO_X_AND_Y_ARRAY_COLLECTION,this.GetXLabel(),this.GetYLabel()) as ArrayCollection);
						//this.dbgText.text += "regular data*" + this.dataParser.GetDebugText();
						//Alert.show("ACData:"+ca.GetArrayCollectionData().length);
					}
					else
					{
						//this.dbgText.text += "both not false";
						if (this.DictArrayValues)
						{
							this.dataParser.SetAggregationData(this.GetAggregationData());
							ca.SetArrayCollectionData(this.dataParser.ParseDataString("AggregatedArray",FormatParser.FROM_COMMA_DATE_METRIC_TO_X_AND_Y_ARRAY_COLLECTION,this.GetXLabel(),this.GetYLabel()) as ArrayCollection);
							
							//this.dbgText.text = "aggregated data*"+this.dataParser.GetDebugText();
							//Alert.show("Aggregated");
							
						}
						
						if (this.DictValues)
						{
							
						}
					}
					
					//this.dbgText.text += "notpiped"+this.dataParser.GetDebugText();
				}
				
				// then to dioctionaty for grid
				this.chartDataArray = this.dataParser.ParseCurrentData(FormatParser.FROM_CURRENT_DATA_TO_ARRAY_COLLECTION,this.GetXLabel(),this.GetYLabel()) as ArrayCollection;
			}
			catch (err:Error)
			{
				Alert.show("[ERROR]problems loading AM Bar chart:"+err.getStackTrace());
			}
		}
		
		private function setAmChartArrayKpiLabelSymbolColor(kpi:String,cat:String,kpiCounters:Dictionary,lgr:AmGraph, settingGraphBalloon:Boolean):void
		{
			var symbolSet:Boolean = false;
			
			if((kpi == "Availability")||(kpi == "Availability"))
			{
				lgr.setStyle("lineColor", "#AA0000");
				
				symbolSet = this.setSymbolByCategory(cat, lgr, kpiCounters, settingGraphBalloon);
				
				if (!symbolSet)// default to old skoool logic
				{
					if (kpiCounters["Availability"] == null)
					{
						kpiCounters["Availability"]  = 0;
						
						//Alert.show("Category is:"+cat);
						
						lgr.setStyle("bullet","round");
						lgr.type = "smoothedLine";
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[0] +" is [[value]]";
						}
		
					}
					else
					{
						kpiCounters["Availability"]++; 
					}
					
					if (kpiCounters["Availability"]== 1)
					{
						lgr.type = "smoothedLine";
						lgr.setStyle("bullet","square");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[1] +" is [[value]]";
						}
					}
					
					if (kpiCounters["Availability"]== 2)
					{
						lgr.type = "smoothedLine";
						lgr.setStyle("bullet", "triangleUp");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[2] +" is [[value]]";
						}
					}
					
					if (kpiCounters["Availability"]== 3)
					{
						lgr.type = "smoothedLine";
						lgr.setStyle("bullet", "triangleDown");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[3] +" is [[value]]";
						}
					}
					
					if (kpiCounters["Availability"]== 4)
					{
						lgr.type = "line";
						lgr.setStyle("bullet","round");
						lgr.setStyle("bulletSize", 10);
						lgr.setStyle("bulletBorderColor", "#FBE870");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[4] +" is [[value]]";
						}
					}
				
					if (kpiCounters["Availability"]== 5)
					{
						lgr.type = "line";
						lgr.setStyle("bullet", "square");
						lgr.setStyle("bulletSize", 10);
						lgr.setStyle("bulletBorderColor", "#FBE870");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[5] +" is [[value]]";
						}
					}
					
					if (kpiCounters["Availability"]== 6)
					{
						lgr.type = "line";
						lgr.setStyle("bullet", "triangleUp");
						lgr.setStyle("bulletSize", 10);
						lgr.setStyle("bulletBorderColor", "#FBE870");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[6] +" is [[value]]";
						}
					}
					
					if (kpiCounters["Availability"]== 7)
					{
						lgr.type = "line";
						lgr.setStyle("bullet", "triangleDown");
						lgr.setStyle("bulletSize", 10);
						lgr.setStyle("bulletBorderColor", "#FBE870");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[7] +" is [[value]]";
						}
					}
				}
			}
			
			
			
			if((kpi == "PLO_Savings")||(kpi == "Optimizer_Savings"))
			{
				lgr.setStyle("lineColor", "#AA0000");
				
				symbolSet = this.setSymbolByCategory(cat, lgr, kpiCounters, settingGraphBalloon);
				
				if (!symbolSet)// default to old skoool logic
				{
					if (kpiCounters["Optimizer_Savings"] == null)
					{
						kpiCounters["Optimizer_Savings"]  = 0;
						
						//Alert.show("Category is:"+cat);
						
						lgr.setStyle("bullet","round");
						lgr.type = "smoothedLine";
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[0] +" is [[value]]";
						}
		
					}
					else
					{
						kpiCounters["Optimizer_Savings"]++; 
					}
					
					if (kpiCounters["Optimizer_Savings"]== 1)
					{
						lgr.type = "smoothedLine";
						lgr.setStyle("bullet","square");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[1] +" is [[value]]";
						}
					}
					
					if (kpiCounters["Optimizer_Savings"]== 2)
					{
						lgr.type = "smoothedLine";
						lgr.setStyle("bullet", "triangleUp");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[2] +" is [[value]]";
						}
					}
					
					if (kpiCounters["Optimizer_Savings"]== 3)
					{
						lgr.type = "smoothedLine";
						lgr.setStyle("bullet", "triangleDown");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[3] +" is [[value]]";
						}
					}
					
					if (kpiCounters["Optimizer_Savings"]== 4)
					{
						lgr.type = "line";
						lgr.setStyle("bullet","round");
						lgr.setStyle("bulletSize", 10);
						lgr.setStyle("bulletBorderColor", "#FBE870");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[4] +" is [[value]]";
						}
					}
				
					if (kpiCounters["Optimizer_Savings"]== 5)
					{
						lgr.type = "line";
						lgr.setStyle("bullet", "square");
						lgr.setStyle("bulletSize", 10);
						lgr.setStyle("bulletBorderColor", "#FBE870");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[5] +" is [[value]]";
						}
					}
					
					if (kpiCounters["Optimizer_Savings"]== 6)
					{
						lgr.type = "line";
						lgr.setStyle("bullet", "triangleUp");
						lgr.setStyle("bulletSize", 10);
						lgr.setStyle("bulletBorderColor", "#FBE870");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[6] +" is [[value]]";
						}
					}
					
					if (kpiCounters["Optimizer_Savings"]== 7)
					{
						lgr.type = "line";
						lgr.setStyle("bullet", "triangleDown");
						lgr.setStyle("bulletSize", 10);
						lgr.setStyle("bulletBorderColor", "#FBE870");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[7] +" is [[value]]";
						}
					}
				}
			}

			if ((kpi == "Reliability")||(kpi == "Reliability"))
			{
				lgr.setStyle("lineColor", "#00AA00");
				
				symbolSet = this.setSymbolByCategory(cat, lgr, kpiCounters, settingGraphBalloon);
				
				if (!symbolSet)// default to old skoool logic
				{
				
					if (kpiCounters["Reliability"] == null)
					{
						kpiCounters["Reliability"]  = 0;
					
						lgr.type = "smoothedLine";
						lgr.setStyle("bullet", "round");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[0] +" is [[value]]";
						}
					}
					else
					{
						kpiCounters["Reliability"]++; 
					}
					
					if (kpiCounters["Reliability"]== 1)
					{
						lgr.type = "smoothedLine";
						lgr.setStyle("bullet", "square");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[1] +" is [[value]]";
						}
					}
					
					if (kpiCounters["Reliability"]== 2)
					{
						lgr.type = "smoothedLine";
						lgr.setStyle("bullet", "triangleUp");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[2] +" is [[value]]";
						}
					}
					
					if (kpiCounters["Reliability"]== 3)
					{
						lgr.type = "smoothedLine";
						lgr.setStyle("bullet", "triangleDown");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[3] +" is [[value]]";
						}
					}
					
					if (kpiCounters["Reliability"]== 4)
					{
						lgr.type = "line";
						lgr.setStyle("bullet", "round");
						lgr.setStyle("bulletSize", 10);
						lgr.setStyle("bulletBorderCOlor", "#FBE870");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[4] +" is [[value]]";
						}
					}
					
					if (kpiCounters["Reliability"]== 5)
					{
						lgr.type = "line";
						lgr.setStyle("bullet", "square");
						lgr.setStyle("bulletSize", 10);
						lgr.setStyle("bulletBorderColor", "#FBE870");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[5] +" is [[value]]";
						}
					}
					
					if (kpiCounters["Reliability"]== 6)
					{
						lgr.type = "line";
						lgr.setStyle("bullet", "triangleUp");
						lgr.setStyle("bulletSize", 10);
						lgr.setStyle("bulletBorderColor", "#FBE870");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[6] +" is [[value]]";
						}
					}
					
					if (kpiCounters["Reliability"]== 7)
					{
						lgr.type = "line";
						lgr.setStyle("bullet", "triangleDown");
						lgr.setStyle("bulletSize", 10);
						lgr.setStyle("bulletBorderColor", "#FBE870");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[7] +" is [[value]]";
						}
					}
				}
			}
			
			if ((kpi == "O2_Score")||(kpi == "Optimizer_O2"))
			{
				lgr.setStyle("lineColor", "#00AA00");
				
				symbolSet = this.setSymbolByCategory(cat, lgr, kpiCounters, settingGraphBalloon);
				
				if (!symbolSet)// default to old skoool logic
				{
				
					if (kpiCounters["Optimizer_O2"] == null)
					{
						kpiCounters["Optimizer_O2"]  = 0;
					
						lgr.type = "smoothedLine";
						lgr.setStyle("bullet", "round");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[0] +" is [[value]]";
						}
					}
					else
					{
						kpiCounters["Optimizer_O2"]++; 
					}
					
					if (kpiCounters["Optimizer_O2"]== 1)
					{
						lgr.type = "smoothedLine";
						lgr.setStyle("bullet", "square");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[1] +" is [[value]]";
						}
					}
					
					if (kpiCounters["Optimizer_O2"]== 2)
					{
						lgr.type = "smoothedLine";
						lgr.setStyle("bullet", "triangleUp");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[2] +" is [[value]]";
						}
					}
					
					if (kpiCounters["Optimizer_O2"]== 3)
					{
						lgr.type = "smoothedLine";
						lgr.setStyle("bullet", "triangleDown");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[3] +" is [[value]]";
						}
					}
					
					if (kpiCounters["Optimizer_O2"]== 4)
					{
						lgr.type = "line";
						lgr.setStyle("bullet", "round");
						lgr.setStyle("bulletSize", 10);
						lgr.setStyle("bulletBorderCOlor", "#FBE870");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[4] +" is [[value]]";
						}
					}
					
					if (kpiCounters["Optimizer_O2"]== 5)
					{
						lgr.type = "line";
						lgr.setStyle("bullet", "square");
						lgr.setStyle("bulletSize", 10);
						lgr.setStyle("bulletBorderColor", "#FBE870");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[5] +" is [[value]]";
						}
					}
					
					if (kpiCounters["Optimizer_O2"]== 6)
					{
						lgr.type = "line";
						lgr.setStyle("bullet", "triangleUp");
						lgr.setStyle("bulletSize", 10);
						lgr.setStyle("bulletBorderColor", "#FBE870");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[6] +" is [[value]]";
						}
					}
					
					if (kpiCounters["Optimizer_O2"]== 7)
					{
						lgr.type = "line";
						lgr.setStyle("bullet", "triangleDown");
						lgr.setStyle("bulletSize", 10);
						lgr.setStyle("bulletBorderColor", "#FBE870");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[7] +" is [[value]]";
						}
					}
				}
			}

			if ((kpi == "N2_UnPack")||(kpi == "UnPack_N2"))
			{
				lgr.setStyle("lineColor", "#87CEFA");
				
				symbolSet = this.setSymbolByCategory(cat, lgr, kpiCounters, settingGraphBalloon);
				
				if (!symbolSet)// default to old skoool logic
				{
					
					if (kpiCounters["UnPack_N2"] == null)
					{
						kpiCounters["UnPack_N2"]  = 0;
						
						lgr.type = "smoothedLine";
						lgr.setStyle("bullet", "round");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[0] +" is [[value]]";
						}
					}
					else
					{
						kpiCounters["UnPack_N2"]++; 
					}
					
					if (kpiCounters["UnPack_N2"]== 1)
					{
						lgr.type = "smoothedLine";
						lgr.setStyle("bullet", "square");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[1] +" is [[value]]";
						}
					}
					
					if (kpiCounters["UnPack_N2"]== 2)
					{
						lgr.type = "smoothedLine";
						lgr.setStyle("bullet", "triangeUp");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[2] +" is [[value]]";
						}
					}
					
					if (kpiCounters["UnPack_N2"]== 3)
					{
						lgr.type = "smoothedLine";
						lgr.setStyle("bullet", "triangleDown");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[3] +" is [[value]]";
						}
					}
					
					if (kpiCounters["UnPack_N2"]== 4)
					{
						lgr.type = "line";
						lgr.setStyle("bullet", "round");
						lgr.setStyle("bulletSize", 10);
						lgr.setStyle("bulletBorderColor", "#FBE870");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[4] +" is [[value]]";
						}
					}
					
					if (kpiCounters["UnPack_N2"]== 5)
					{
						lgr.type = "line";
						lgr.setStyle("bullet", "square");
						lgr.setStyle("bulletSize", 10);
						lgr.setStyle("bulletBorderColor", "#FBE870");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[5] +" is [[value]]";
						}
					}
					
					if (kpiCounters["UnPack_N2"]== 6)
					{
						lgr.type = "line";
						lgr.setStyle("bullet", "triangleUp");
						lgr.setStyle("bulletSize", 10);
						lgr.setStyle("bulletBorderColor", "#FBE870");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[6] +" is [[value]]";
						}
					}
					
					if (kpiCounters["UnPack_N2"]== 7)
					{
						lgr.type = "line";
						lgr.setStyle("bullet", "triangleDown");
						lgr.setStyle("bulletSize", 10);
						lgr.setStyle("bulletBorderColor", "#FBE870");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[7] +" is [[value]]";
						}
					}				
				}
			}
										
			if ((kpi == "GEI")||(kpi == "GEI_O2"))
			{
				lgr.setStyle("lineColor", "#FF6103");
				
				symbolSet = this.setSymbolByCategory(cat, lgr, kpiCounters, settingGraphBalloon);
				
				if (!symbolSet)// default to old skoool logic
				{
				
					if (kpiCounters["GEI_O2"] == null)
					{
						kpiCounters["GEI_O2"]  = 0;
						
						lgr.type = "smoothedLine";
						lgr.setStyle("bullet", "round");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[0] +" is [[value]]";
						}
					}
					else
					{
						kpiCounters["GEI_O2"]++; 
					}
					
					if (kpiCounters["GEI_O2"]== 1)
					{
						lgr.type = "smoothedLine";
						lgr.setStyle("bullet","square");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[1] +" is [[value]]";
						}
					}
					
					if (kpiCounters["GEI_O2"]== 2)
					{
						lgr.type = "smoothedLine";
						lgr.setStyle("bullet", "triangleUp");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[2] +" is [[value]]";
						}
					}
					
					if (kpiCounters["GEI_O2"]== 3)
					{
						lgr.type = "smoothedLine";
						lgr.setStyle("bullet", "triangleDown");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[3] +" is [[value]]";
						}
					}
					
					if (kpiCounters["GEI_O2"]== 4)
					{
						lgr.type = "line";
						lgr.setStyle("bullet", "round");
						lgr.setStyle("bulletSize", 10);
						lgr.setStyle("bulletBorderColor", "#FBE870");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[4] +" is [[value]]";
						}
					}
					
					if (kpiCounters["GEI_O2"]== 5)
					{
						lgr.type = "line";
						lgr.setStyle("bullet", "square");
						lgr.setStyle("bulletSize", 10);
						lgr.setStyle("bulletBorderColor", "#FBE870");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[5] +" is [[value]]";
						}
					}
					
					if (kpiCounters["GEI_O2"]== 6)
					{
						lgr.type = "line";
						lgr.setStyle("bullet", "triangleUp");
						lgr.setStyle("bulletSize", 10);
						lgr.setStyle("bulletBorderColor", "#FBE870");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[6] +" is [[value]]";
						}
					}
					
					if (kpiCounters["GEI_O2"]== 7)
					{
						lgr.type = "line";
						lgr.setStyle("bullet", "triangleDown");
						lgr.setStyle("bulletSize", 10);
						lgr.setStyle("bulletBorderColor", "#FBE870");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[7] +" is [[value]]";
						}
					}				
				}
			}

			if ((kpi == "VMESA")||(kpi == "Mesa_savings"))
			{
				lgr.setStyle("lineColor", "#FF0000");
				
				
				symbolSet = this.setSymbolByCategory(cat, lgr, kpiCounters, settingGraphBalloon);
				
				if (!symbolSet)// default to old skoool logic
				{
					
					if (kpiCounters["Mesa_savings"] == null)
					{
						kpiCounters["Mesa_savings"]  = 0;
						
						lgr.type = "smoothedLine";
						lgr.setStyle("bullet", "round");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[0] +" is [[value]]";
						}
					}
					else
					{
						kpiCounters["Mesa_savings"]++; 
					}
					
					if (kpiCounters["Mesa_savings"]== 1)
					{
						lgr.type = "smoothedLine";
						lgr.setStyle("bullet", "square");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[1] +" is [[value]]";
						}
					}
					
					if (kpiCounters["Mesa_savings"]== 2)
					{
						lgr.type = "smoothedLine";
						lgr.setStyle("bullet", "triangleUp");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[2] +" is [[value]]";
						}
					}
					
					if (kpiCounters["Mesa_savings"]== 3)
					{
						lgr.type = "line";
						lgr.setStyle("bullet", "triangleDown");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[3] +" is [[value]]";
						}
					}
					
					if (kpiCounters["Mesa_savings"]== 4)
					{
						lgr.type = "line";
						lgr.setStyle("bullet", "round");
						lgr.setStyle("bulletSize",10);
						lgr.setStyle("bulletBorderColor", "#FBE870");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[4] +" is [[value]]";
						}
					}
					
					if (kpiCounters["Mesa_savings"]== 5)
					{
						lgr.type = "smoothedLine";
						lgr.setStyle("bullet", "square");
						lgr.setStyle("bulletSize", 10);
						lgr.setStyle("bulletBorderColor", "#FBE870");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[5] +" is [[value]]";
						}
					}
					
					if (kpiCounters["Mesa_savings"]== 6)
					{
						lgr.type = "line";
						lgr.setStyle("bullet", "triangleUp");
						lgr.setStyle("bulletSize", 10);
						lgr.setStyle("bulletBorderColor", "#FBE870");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[6] +" is [[value]]";
						}
					}
					
					if (kpiCounters["Mesa_savings"]== 7)
					{
						lgr.type = "line";
						lgr.setStyle("bullet", "triangleDown");
						lgr.setStyle("bulletSize", 10);
						lgr.setStyle("bulletBorderColor", "#FBE870");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[7] +" is [[value]]";
						}
					}				
				}
			}

			if ((kpi == "SCE")||(kpi == "Avg_SCE"))
			{
				lgr.setStyle("lineColor", "#00FF00");
				
				symbolSet = this.setSymbolByCategory(cat, lgr, kpiCounters, settingGraphBalloon);
				
				if (!symbolSet)// default to old skoool logic
				{
					
					if (kpiCounters["Avg_SCE"] == null)
					{
						kpiCounters["Avg_SCE"]  = 0;
						
						lgr.type = "smoothedLine";
						lgr.setStyle("bullet","round");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[0] +" is [[value]]";
						}
					}
					else
					{
						kpiCounters["Avg_SCE"]++; 
					}
					
					if (kpiCounters["Avg_SCE"]== 1)
					{
						lgr.type = "line";
						lgr.setStyle("bullet", "square");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[1] +" is [[value]]";
						}
					}
					
					if (kpiCounters["Avg_SCE"]== 2)
					{
						lgr.type = "smoothedLine";
						lgr.setStyle("bullet", "triangleUp");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[2] +" is [[value]]";
						}
					}
					
					if (kpiCounters["Avg_SCE"]== 3)
					{
						lgr.type = "line";
						lgr.setStyle("bullet", "triangleDown");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[3] +" is [[value]]";
						}
					}
					
					if (kpiCounters["Avg_SCE"]== 4)
					{
						lgr.type = "line";
						lgr.setStyle("bullet", "round");
						lgr.setStyle("bulletSize", 10);
						lgr.setStyle("bulletBorderColor","#FBE870");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[4] +" is [[value]]";
						}
					}
					
					if (kpiCounters["Avg_SCE"]== 5)
					{
						lgr.type = "line";
						lgr.setStyle("bullet", "square");
						lgr.setStyle("bulletSize", 10);
						lgr.setStyle("bulletBorderColor","#FBE870");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[5] +" is [[value]]";
						}
					}
					
					if (kpiCounters["Avg_SCE"]== 6)
					{
						lgr.type = "smoothedLine";
						lgr.setStyle("bullet", "triangleUp");
						lgr.setStyle("bulletSize", 10);
						lgr.setStyle("bulletBorderColor", "#FBE870");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[6] +" is [[value]]";
						}
					}
					
					if (kpiCounters["Avg_SCE"]== 7)
					{
						lgr.type = "line";
						lgr.setStyle("bullet", "triangleDown");
						lgr.setStyle("bulletSize", 10);
						lgr.setStyle("bulletBorderColor", "#FBE870");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[7] +" is [[value]]";
						}
					}				
				}
			}

			if ((kpi == "H2_Model"))
			{
				lgr.setStyle("lineColor", "#0000FF");
				
				symbolSet = this.setSymbolByCategory(cat, lgr, kpiCounters, settingGraphBalloon);
				
				if (!symbolSet)// default to old skoool logic
				{
				
					if (kpiCounters["H2_Model"] == null)
					{
						kpiCounters["H2_Model"]  = 0;
						
						lgr.type = "smoothedLine";
						lgr.setStyle("bullet","round");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[0] +" is [[value]]";
						}
					}
					else
					{
						kpiCounters["H2_Model"]++; 
					}
					
					if (kpiCounters["H2_Model"]== 1)
					{
						lgr.type = "smoothedLine";
						lgr.setStyle("bullet", "square");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[1] +" is [[value]]";
						}
					}
					
					if (kpiCounters["H2_Model"]== 2)
					{
						lgr.type = "smoothedLine";
						lgr.setStyle("bullet", "triangleUp");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[2] +" is [[value]]";
						}
					}
					
					if (kpiCounters["H2_Model"]== 3)
					{
						lgr.type = "smoothedLine";
						lgr.setStyle("bullet", "triangleDown");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[3] +" is [[value]]";
						}
					}
					
					if (kpiCounters["H2_Model"]== 4)
					{
						lgr.type = "line";
						lgr.setStyle("bullet", "round");
						lgr.setStyle("bulletSize", 10);
						lgr.setStyle("bulletBorderColor", "#FBE870");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[4] +" is [[value]]";
						}
					}
					
					if (kpiCounters["H2_Model"]== 5)
					{
						lgr.type = "line";
						lgr.setStyle("bullet", "square");
						lgr.setStyle("bulletSize", 10);
						lgr.setStyle("bulletBorderColor", "#FBE870");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[5] +" is [[value]]";
						}
					}
					
					if (kpiCounters["H2_Model"]== 6)
					{
						lgr.type = "line";
						lgr.setStyle("bullet", "triangleUp");
						lgr.setStyle("bulletSize", 10);
						lgr.setStyle("bulletBorderColor", "#FBE870");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[6] +" is [[value]]";
						}
					}
					
					if (kpiCounters["H2_Model"]== 7)
					{
						lgr.type = "line";
						lgr.setStyle("bullet", "triangleDown");
						lgr.setStyle("bulletSize", 10);
						lgr.setStyle("bulletBorderColor", "#FBE870");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[7] +" is [[value]]";
						}
					}				
				}
			}

			if (kpi == "Excess_Steam")
			{
				lgr.setStyle("lineColor", "#0B0B0B");
				
				symbolSet = this.setSymbolByCategory(cat, lgr, kpiCounters, settingGraphBalloon);
				
				if (!symbolSet)// default to old skoool logic
				{
				
					if (kpiCounters["Excess_Steam"] == null)
					{
						kpiCounters["Excess_Steam"]  = 0;
						
						lgr.type = "smoothedLine";
						lgr.setStyle("bullet", "round");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[0] +" is [[value]]";
						}

					}
					else
					{
						kpiCounters["Excess_Steam"]++; 
					}
					
					if (kpiCounters["Excess_Steam"]== 1)
					{
						lgr.type = "smoothedLine";
						lgr.setStyle("bullet", "square");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[1] +" is [[value]]";
						}
					}
					
					if (kpiCounters["Excess_Steam"]== 2)
					{
						lgr.type = "smoothedLine";
						lgr.setStyle("bullet", "triangleUp");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[2] +" is [[value]]";
						}
					}
					
					if (kpiCounters["Excess_Steam"]== 3)
					{
						lgr.type = "smoothedLine";
						lgr.setStyle("bullet", "triangleDown");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[3] +" is [[value]]";
						}
					}
					
					if (kpiCounters["Excess_Steam"]== 4)
					{
						lgr.type = "line";
						lgr.setStyle("bullet","round");
						lgr.setStyle("bulletSize", 10);
						lgr.setStyle("bulletBorderColor", "#FBE870");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[4] +" is [[value]]";
						}
					}
					
					if (kpiCounters["Excess_Steam"]== 5)
					{
						lgr.type = "line";
						lgr.setStyle("bullet", "square");
						lgr.setStyle("bulletSize", 10);
						lgr.setStyle("bulletBorderColor", "#FBE870");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[5] +" is [[value]]";
						}
					}
					
					if (kpiCounters["Excess_Steam"]== 6)
					{
						lgr.type = "line";
						lgr.setStyle("bullet", "triangleUp");
						lgr.setStyle("bulletSize", 10);
						lgr.setStyle("bulletBorderColor", "#FBE870");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[6] +" is [[value]]";
						}
					}
					
					if (kpiCounters["Excess_Steam"]== 7)
					{
						lgr.type = "line";
						lgr.setStyle("bullet", "triangleDown");
						lgr.setStyle("bulletSize", 10);
						lgr.setStyle("bulletBorderColor", "#FBE870");
						if (settingGraphBalloon)
						{
							lgr.balloonText =  " Score for " + this.GetKPICategory()[7] +" is [[value]]";
						}
					}				
				}
			}
		}
		
		private function setAmChartKpiLabelLineColor(kpi:String, g:AmGraph, styleOnly:Boolean):void
		{
			if((kpi == "Availability")||(kpi == "Availability"))
			{
				if (!styleOnly)
				{
					g.balloonText = "Availability" + " Percent is [[value]]";
					g.title = "Availability";
				}
				
				g.setStyle("lineColor", "#AA0000");
				//g.legendKeyColor = 0xAA0000;
				//Alert.show("hit PLO");
			}
			
			if((kpi == "PLO_Savings")||(kpi == "Optimizer_Savings"))
			{
				if (!styleOnly)
				{
					g.balloonText = "PLO Savings" + " Score is [[value]]";
					g.title = "PLO Savings";
				}
				
				g.setStyle("lineColor", "#AA0000");
				//g.legendKeyColor = 0xAA0000;
				//Alert.show("hit PLO");
			}

			if ((kpi == "Reliability")||(kpi == "Reliability"))
			{
				if (!styleOnly)
				{
					g.balloonText = "Reliability" + " Score is [[value]]";
					g.title = "Reliability Score";
				}
				
				//g.legendKeyColor = 0x00AA00;
				g.setStyle("lineColor","#00AA00");
			}
			
			if ((kpi == "O2_Score")||(kpi == "Optimizer_O2"))
			{
				if (!styleOnly)
				{
					g.balloonText = "O2 Score" + " Score is [[value]]";
					g.title = "O2 Score";
				}
				
				//g.legendKeyColor = 0x00AA00;
				g.setStyle("lineColor","#00AA00");
			}

			if ((kpi == "N2_UnPack")||(kpi == "UnPack_N2"))
			{
				if (!styleOnly)
				{
					g.balloonText = "N2 UnPack" + " Score is [[value]]";
					g.title = "N2 UnPack";
				}
				
				//g.legendKeyColor = 0x87CEFA;
				g.setStyle("lineColor","#87CEFA");
			}
					
			if ((kpi == "GEI")||(kpi == "GEI_O2"))
			{
				if (!styleOnly)
				{
					g.balloonText = "GEI" + " Score is [[value]]";
					g.title = "GEI";
				}
				
				//g.legendKeyColor = 0xFF6103;
				g.setStyle("lineColor", "#FF6103");
			}

			if ((kpi == "VMESA")||(kpi == "Mesa_savings"))
			{
				if (!styleOnly)
				{
					g.balloonText = "VMESA" + " Score is [[value]]";
					g.title = "VMESA";
				}
				
				//g.legendKeyColor = 0xFF0000;
				g.setStyle("lineColor", "#FF0000");
				
				//Alert.show("hit VMESA");
			
			}

			if ((kpi == "SCE")||(kpi == "Avg_SCE"))
			{
				if (!styleOnly)
				{
					g.balloonText = "SCE" + " Score is [[value]]";
					g.title = "SCE";
				}
				
				//g.legendKeyColor = 0x00FF00;
				g.setStyle("lineColor", "#00FF00");
			}

			if (kpi == "H2_Model")
			{
				if (!styleOnly)
				{
					g.balloonText = "H2 Model" + " Score is [[value]]";
					g.title = "H2 Model";
				}
				
				//g.legendKeyColor = 0x0000FF;
				g.setStyle("lineColor", "#0000FF");
			}

			if (kpi == "Excess_Steam")
			{
				if (!styleOnly)
				{
					g.balloonText = "Excess Steam" + " Score is [[value]]";
					g.title = "Excess Steam";
				}
				
				//g.legendKeyColor = 0x0B0B0B;
				g.setStyle("lineColor", "#0B0B0B");
			}
		}
		
		private function setSymbolByCategory(cat:String, lgr:AmGraph, kpiCounters:Dictionary, settingGraphBalloon:Boolean):Boolean
		{
			
			var symbolSet:Boolean = false;
			
				if (cat.indexOf("Evans") >= 0)
				{
					symbolSet = true;
					lgr.setStyle("bullet", "round");
					lgr.type = "smoothedLine";
					if (settingGraphBalloon)
					{
						lgr.balloonText =  " Score for " + this.GetKPICategory()[kpiCounters["Optimizer_Savings"]] +" is [[value]]";
					}
				}
				
				if(cat.indexOf("George") >= 0 )
				{
					symbolSet = true;
					lgr.setStyle("bullet", "square");
					lgr.type = "smoothedLine";
					if (settingGraphBalloon)
					{
						lgr.balloonText =  " Score for " + this.GetKPICategory()[kpiCounters["Optimizer_Savings"]] +" is [[value]]";
					}
				}
				
				if (cat.indexOf("Stanford") >= 0)
				{
					symbolSet = true;
					lgr.setStyle("bullet", "triangleUp");
					lgr.type = "smoothedLine";
					if (settingGraphBalloon)
					{
						lgr.balloonText =  " Score for " + this.GetKPICategory()[kpiCounters["Optimizer_Savings"]] +" is [[value]]";
					}
				}
				
				if (cat.indexOf("Adams") >= 0)
				{
					symbolSet = true;
					lgr.setStyle("bullet", "triangleDown");
					lgr.type = "smoothedLine";
					if (settingGraphBalloon)
					{
						lgr.balloonText =  " Score for " + this.GetKPICategory()[kpiCounters["Optimizer_Savings"]] +" is [[value]]";
					}
				}
				
				if (cat.indexOf("Fox") >= 0)
				{
					symbolSet = true;
					lgr.setStyle("bullet", "round");
					lgr.setStyle("bulletSize", 10);
					lgr.setStyle("bulletBorderColor", "#FBE870");
					lgr.type = "line";
					if (settingGraphBalloon)
					{
						lgr.balloonText =  " Score for " + this.GetKPICategory()[kpiCounters["Optimizer_Savings"]] +" is [[value]]";
					}
				}
				
				if (cat.indexOf("Salinas") >= 0)
				{
					symbolSet = true;
					lgr.setStyle("bullet", "square");
					lgr.setStyle("bulletSize", 10);
					lgr.setStyle("bulletBorderColor", "#FBE870");
					lgr.type = "line";
					if (settingGraphBalloon)
					{
						lgr.balloonText =  " Score for " + this.GetKPICategory()[kpiCounters["Optimizer_Savings"]] +" is [[value]]";
					}
				}
				
				if (cat.indexOf("Roberts") >= 0)
				{
					symbolSet = true;
					lgr.setStyle("bullet", "triangleUp");
					lgr.setStyle("bulletSize", 10);
					lgr.setStyle("bulletBorderColor", "#FBE870");
					lgr.type = "line";
					if (settingGraphBalloon)
					{
						lgr.balloonText =  " Score for " + this.GetKPICategory()[kpiCounters["Optimizer_Savings"]] +" is [[value]]";
					}
				}
				
				if (cat.indexOf("John") >= 0)
				{
					symbolSet = true;
					lgr.setStyle("bullet", "triangleDown");
					lgr.setStyle("bulletSize", 10);
					lgr.setStyle("bulletBorderColor", "#FBE870");
					lgr.type = "line";
					if (settingGraphBalloon)
					{
						lgr.balloonText =  " Score for " + this.GetKPICategory()[kpiCounters["Optimizer_Savings"]] +" is [[value]]";
					}
				}
				
			return symbolSet;
		}
	}
}