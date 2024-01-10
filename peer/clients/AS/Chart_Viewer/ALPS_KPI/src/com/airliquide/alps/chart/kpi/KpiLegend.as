package com.airliquide.alps.chart.kpi 
{
	// AS3 API
	import flash.display.DisplayObject;
	import flash.display.Shape;
	import flash.display.Sprite;
	import flash.events.MouseEvent;
	import flash.text.TextField;
	import flash.text.TextFormat;
	import flash.utils.Dictionary;
	
	// FLEX API
	import mx.controls.Label;
	import mx.core.UIComponent;
	import mx.controls.Alert;
	import mx.collections.*;
	
	// Am chart API
	import com.amcharts.AmSerialChart;
	import com.amcharts.events.GraphEvent;
	import com.amcharts.chartClasses.*;
	
	
	public class KpiLegend extends UIComponent
	{
		//[Embed(systemFont='Arial', fontName='spArial', mimeType='application/x-font')]
		[Embed(systemFont='Verdana', fontName='spVerdana', mimeType='application/x-font')]
		public static var ArualFont:Class;
		
		public var Circle:Sprite;
		public var Square:Sprite;
		public var GridRow:Sprite;
		
		public var TriangleUp:Shape;
		public var TriangleDown:Shape;
		
		private var values:Array;
		private var fieldLabels:Array;
		private var kCharts:Array;
		private var legendAData:Array;
		
		private var legendACData:ArrayCollection;
		
		private var kCounter:Dictionary;
		private var legendDData:Dictionary;
		private var printedCategory:Dictionary;
		
		private var test:TextField;
		private var test2:TextField;
		
		private var testFormat:TextFormat;
		private var testFormat2:TextFormat;
		
		public function KpiLegend() 
		{
			super();
			
			this.kCharts = new Array();
			
			// initialize counts for kpi
			kCounter = new Dictionary();
			kCounter["PLO_Savings"] = 0;
			kCounter["O2_Score"] = 0;
			kCounter["GEI"] = 0;
			kCounter["N2_UnPack"] = 0;
			kCounter["VMESA"] = 0;
			kCounter["SCE"] = 0;
			kCounter["H2_Model"] = 0;
			kCounter["Excess_Steam"] = 0;
			kCounter["Availability"] = 0;
			kCounter["Reliability"] = 0;
			
			//this.width = 450;
			this.height = 300;
			
			printedCategory = new Dictionary();
			
		}
		
		public function SetDataProvider(data:Object, type:String):void
		{
			if (type == "Dict")
			{
				this.legendDData = data as Dictionary;
				//this.drawGridLegend();
				//this.width = 450;
			}
			
			if (type == "Array")
			{
				this.legendAData = data as Array;
				this.drawGridLegend();
			    this.width = 450;
			}
			
			
			
			if (type == "GraphArray")
			{
				this.legendAData = data as Array;
				this.drawBasicLegend();
				this.width = 350;
			}
			
			if (type == "ArrayCollection")
			{
				this.legendACData = data as ArrayCollection;
				this.drawBasicLegend();
				this.width = 350;
			}
			
			
			
		}
		
		public function HandleChartMouseOver(chart:GraphEvent):void
		{
			var gdi:GraphDataItem = chart.item;
			var textObject:TextField;
			
			try
			{
				//Alert.show(chart.graph.title);
				if (chart.graph.title.indexOf("PLO") >= 0)
				{
				
					if (legendDData != null)
					{
						textObject = this.legendDData["PLO_Savings"] as TextField;
						if (textObject != null)
						{
							if (textObject.text.indexOf(">") < 0)
							{
								textObject.appendText(" >" + gdi.values.value);	
							}
							else
							{
								textObject.text = chart.graph.title + " >" + gdi.values.value;
							}
						}
					}
				}
				
				if (chart.graph.title.indexOf("O2 Score") >= 0)
				{
					//Alert.show("O2!");
					if (legendDData != null)
					{
						textObject = this.legendDData["O2_Score"] as TextField;
						if (textObject != null)
						{
							if (textObject.text.indexOf(">") < 0)
							{
								textObject.appendText(" >" + gdi.values.value);	
							}
							else
							{
								textObject.text = chart.graph.title + " >" + gdi.values.value;
							}
						}
					}
				}
				
				
				if (chart.graph.title.indexOf("GEI") >= 0)
				{
				
					if (legendDData != null)
					{
						textObject = this.legendDData["GEI"] as TextField;
						if (textObject != null)
						{
							if (textObject.text.indexOf(">") < 0)
							{
								textObject.appendText(" >" + gdi.values.value);	
							}
							else
							{
								textObject.text = chart.graph.title + " >" + gdi.values.value;
							}
						}
					}
				}
				
				if (chart.graph.title.indexOf("N2 UnPack") >= 0)
				{
				
					if (legendDData != null)
					{
						textObject = this.legendDData["N2_UnPack"] as TextField;
						if (textObject != null)
						{
							if (textObject.text.indexOf(">") < 0)
							{
								textObject.appendText(" >" + gdi.values.value);	
							}
							else
							{
								textObject.text = chart.graph.title + " >" + gdi.values.value;
							}
						}
					}
				}
				
				if (chart.graph.title.indexOf("SCE") >= 0)
				{
				
					if (legendDData != null)
					{
						textObject = this.legendDData["SCE"] as TextField;
						if (textObject != null)
						{
							if (textObject.text.indexOf(">") < 0)
							{
								textObject.appendText(" >" + gdi.values.value);	
							}
							else
							{
								textObject.text = chart.graph.title + " >" + gdi.values.value;
							}
						}
					}
				}
				
				if (chart.graph.title.indexOf("VMESA") >= 0)
				{
				
					if (legendDData != null)
					{
						textObject = this.legendDData["VMESA"] as TextField;
						if (textObject != null)
						{
							if (textObject.text.indexOf(">") < 0)
							{
								textObject.appendText(" >" + gdi.values.value);	
							}
							else
							{
								textObject.text = chart.graph.title + " >" + gdi.values.value;
							}
						}
					}
				}
					
				if (chart.graph.title.indexOf("H2 Model") >= 0)
				{
				
					if (legendDData != null)
					{
						textObject = this.legendDData["H2_Model"] as TextField;
						if (textObject != null)
						{
							if (textObject.text.indexOf(">") < 0)
							{
								textObject.appendText(" >" + gdi.values.value);	
							}
							else
							{
								textObject.text = chart.graph.title + " >" + gdi.values.value;
							}
						}
					}
				}
					
				if (chart.graph.title.indexOf("Excess Steam") >= 0)
				{
				
					if (legendDData != null)
					{
						textObject = this.legendDData["Excess_Steam"] as TextField;
						if (textObject != null)
						{
							if (textObject.text.indexOf(">") < 0)
							{
								textObject.appendText(" >" + gdi.values.value);	
							}
							else
							{
								textObject.text = chart.graph.title + " >" + gdi.values.value;
							}
						}
					}
				}
			}
			catch (err:Error)
			{
				Alert.show("[ERROR]"+err.getStackTrace());
			}
			
		}
		
		public function HandleChartMouseOut(chart:GraphEvent):void
		{
			var gdi:GraphDataItem = chart.item;
			var textObject:TextField;
			
			try
			{
				if (chart.graph.title.indexOf("PLO") >= 0)
				{	
					if (legendDData != null)
					{
						textObject = this.legendDData["PLO_Savings"] as TextField;
						if (textObject != null)
						{
							textObject.text = chart.graph.title;
						}
					}
				}
				
				if (chart.graph.title.indexOf("O2 Score") >= 0)
				{	
					if (legendDData != null)
					{
						textObject = this.legendDData["O2_Score"] as TextField;
						if (textObject != null)
						{
							textObject.text = chart.graph.title;
						}
					}
				}
				
				if (chart.graph.title.indexOf("GEI") >= 0)
				{	
					if (legendDData != null)
					{
						textObject = this.legendDData["GEI"] as TextField;
						if (textObject != null)
						{
							textObject.text = chart.graph.title;
						}
					}
				}
				
				if (chart.graph.title.indexOf("N2 UnPack") >= 0)
				{	
					if (legendDData != null)
					{
						textObject = this.legendDData["N2_UnPack"] as TextField;
						if (textObject != null)
						{
							textObject.text = chart.graph.title;
						}
					}
				}
				
				if (chart.graph.title.indexOf("VMESA") >= 0)
				{	
					if (legendDData != null)
					{
						textObject = this.legendDData["VMESA"] as TextField;
						if (textObject != null)
						{
							textObject.text = chart.graph.title;
						}
					}
				}
				
				if (chart.graph.title.indexOf("SCE") >= 0)
				{	
					if (legendDData != null)
					{
						textObject = this.legendDData["SCE"] as TextField;
						if (textObject != null)
						{
							textObject.text = chart.graph.title;
						}
					}
				}
				
				if (chart.graph.title.indexOf("H2 Model") >= 0)
				{	
					if (legendDData != null)
					{
						textObject = this.legendDData["H2_Model"] as TextField;
						if (textObject != null)
						{
							textObject.text = chart.graph.title;
						}
					}
				}
				
				if (chart.graph.title.indexOf("Excess Steam") >= 0)
				{	
					if (legendDData != null)
					{
						textObject = this.legendDData["Excess_Steam"] as TextField;
						if (textObject != null)
						{
							textObject.text = chart.graph.title;
						}
					}
				}
			}
			catch (err:Error)
			{
				Alert.show("[ERROR]"+err.getStackTrace());
			}
			
		}
		
		public function HandleChartClick(chart:GraphEvent):void
		{
			//if (chart.index < 10)
			{
				//Alert.show("Handled a graph click event");
			}
		}
		
		private function drawBasicLegend():void
		{
			var chartArray:Array = legendAData;
			var individualChart:AmGraph;
			
			var value:String;
			var valueMax:String;
			var kpiName:String;
			var legendLabel:String;
			
			var test3:TextField = new TextField();
			testFormat2 = new TextFormat("Verdana",9, 0x000000);
			
			if (this.legendDData == null)
			{
				this.legendDData = new Dictionary();
			}
			
			try
			{
				//Alert.show("chart length:"+chartArray.length);
				
				for (var b:Number = 0; b < chartArray.length; b++ )
				{
					individualChart = chartArray[b] as AmGraph;
					//Alert.show("here:" + individualChart.title);
					
					if ((individualChart.title.indexOf("Optimizer Savings")>=0) || (individualChart.title.indexOf("PLO Savings")>=0))
					{
						this.addSquare(0xAA0000,(0),20 * (b +1), false);
						
						if (legendDData["PLO_Savings"]== null)
						{
							legendLabel = individualChart.title
						}
						else
						{
							legendLabel = individualChart.title + " 	" + individualChart.legendValueText;
								
						}
						
						test3 = new TextField();
						test3.x = 25;
						test3.y = 20 * (b +1);
						test3.text = legendLabel;
						test3.setTextFormat(testFormat2);
						this.addChild(test3);
						legendDData["PLO_Savings"] = test3;
						//Alert.show("Added PLO");
					}
					
					if ((individualChart.title.indexOf("Optimizer O2")>=0) || (individualChart.title.indexOf("O2 Score")>=0))
					{
						this.addSquare(0x00AA00,(0),20 * (b+1), false);
						
						if (legendDData["O2_Score"]== null)
						{
							legendLabel = individualChart.title
						}
						else
						{
							legendLabel = individualChart.title + " 	" + individualChart.legendValueText;
								
						}
						
						
						test3 = new TextField();
						test3.x = 25;
						test3.y = 20 * (b +1);
						test3.text = legendLabel;
						test3.setTextFormat(testFormat2);
						this.addChild(test3);
						legendDData["O2_Score"] = test3;
						
					}
					
					if ((individualChart.title.indexOf("GEI")>=0) || (individualChart.title.indexOf("GEI O2")>=0))
					{
						this.addSquare(0xFF6103, (0),20 * (b+1), false);
						
						if (legendDData["GEI"]== null)
						{
							legendLabel = individualChart.title
						}
						else
						{
							legendLabel = individualChart.title + " 	" + individualChart.legendValueText;
								
						}
						
						
						test3 = new TextField();
						test3.x = 25;
						test3.y = 20 * (b +1);
						test3.text = legendLabel;
						test3.setTextFormat(testFormat2);
						this.addChild(test3);
						legendDData["GEI"] = test3;
					}
					
					if ((individualChart.title.indexOf("N2 UnPack")>=0) || (individualChart.title.indexOf("UnPack N2")>=0))
					{
						this.addSquare(0x87CEFA,(0),20 * (b +1), false);
						
						if (legendDData["N2_UnPack"]== null)
						{
							legendLabel = individualChart.title
						}
						else
						{
							legendLabel = individualChart.title + " 	" + individualChart.legendValueText;
								
						}
						
						if (b < 4)
						{
							test3 = new TextField();
							test3.x = 25;
							test3.y = 20 * (b +1);
							test3.text = legendLabel;
							test3.setTextFormat(testFormat2);
							this.addChild(test3);
						}
						else
						{
							test3 = new TextField();
							test3.x = 125;
							test3.y = 120 * (b +1);
							test3.text = legendLabel;
							test3.setTextFormat(testFormat2);
							this.addChild(test3);
						}
						
						legendDData["N2_UnPack"] = test3;
					}
					
					if ((individualChart.title.indexOf("Mesa Savings")>=0) || (individualChart.title.indexOf("VMESA")>=0))
					{
						this.addSquare(0xFF0000,100 + (55),20 * (0 +1), false);
						
						if (legendDData["VMESA"]== null)
						{
							legendLabel = individualChart.title
						}
						else
						{
							legendLabel = individualChart.title + " 	" + individualChart.legendValueText;
								
						}
						
						if (b < 4)
						{
							test3 = new TextField();
							test3.x = 25;
							test3.y = 20 * (b +1);
							test3.text = legendLabel;
							test3.setTextFormat(testFormat2);
							this.addChild(test3);
						}
						else
						{
							test3 = new TextField();
							test3.x = 180;
							test3.y = 20 * (0 +1);
							test3.text = legendLabel;
							test3.setTextFormat(testFormat2);
							this.addChild(test3);
						}
						
						this.legendDData["VMESA"] = test3;
					}
					
					if ((individualChart.title.indexOf("SCE")>=0) || (individualChart.title.indexOf("Avg SCE O2")>=0))
					{
						this.addSquare(0x00FF00,100 + (55),20 * (1 +1), false);
						
						if (legendDData["SCE"]== null)
						{
							legendLabel = individualChart.title
						}
						else
						{
							legendLabel = individualChart.title + " 	" + individualChart.legendValueText;
								
						}
						
						if (b < 4)
						{
							test3 = new TextField();
							test3.x = 25;
							test3.y = 20 * (b +1);
							test3.text = legendLabel;
							test3.setTextFormat(testFormat2);
							this.addChild(test3);
						}
						else
						{
							test3 = new TextField();
							test3.x = 180;
							test3.y = 20 * (1 +1);
							test3.text = legendLabel;
							test3.setTextFormat(testFormat2);
							this.addChild(test3);
						}
						
						this.legendDData["SCE"] = test3;
					}
					
					if ((individualChart.title.indexOf("H2 Model")>=0))
					{
						this.addSquare(0x0000FF,100 + (55),20 * (2 +1), false);
						
						if (legendDData["H2_Model"]== null)
						{
							legendLabel = individualChart.title
						}
						else
						{
							legendLabel = individualChart.title + " 	" + individualChart.legendValueText;
								
						}
						
						if (b < 4)
						{
							test3 = new TextField();
							test3.x = 25;
							test3.y = 20 * (b +1);
							test3.text = legendLabel;
							test3.setTextFormat(testFormat2);
							this.addChild(test3);
						}
						else
						{
							test3 = new TextField();
							test3.x = 180;
							test3.y = 20 * (2 +1);
							test3.text = legendLabel;
							test3.setTextFormat(testFormat2);
							this.addChild(test3);
						}
						
						this.legendDData["H2_Model"] = test3;
					}
					
					if ((individualChart.title.indexOf("Excess Steam")>=0))
					{
						this.addSquare(0x0B0B0B, 100 + (55),20 * (3 +1), false);
						
						if (legendDData["Excess_Steam"]== null)
						{
							legendLabel = individualChart.title
						}
						else
						{
							legendLabel = individualChart.title + " 	" + individualChart.legendValueText;
								
						}
						
						if (b < 4)
						{
							test3 = new TextField();
							test3.x = 25;
							test3.y = 20 * (b +1);
							test3.text = legendLabel;
							test3.setTextFormat(testFormat2);
							this.addChild(test3);
						}
						else
						{
							test3 = new TextField();
							test3.x = 180;
							test3.y = 20 * (3 +1);
							test3.text = legendLabel;
							test3.setTextFormat(testFormat2);
							this.addChild(test3);
						}
						
						this.legendDData["Excess_Steam"] = test3;
					}
				}
			}
			catch (error:Error)
			{
				Alert.show("[ERROR]"+error.getStackTrace());
			}
			
		}
		
		private function drawGridLegend():void
		{
			var chartArray:Array = legendAData;
			var individualChart:Dictionary;
			
			var category:Array;
			var kpiName:String;
			var setOccurance:Number;
			
			try
			{
				//Alert.show("chart len:"+chartArray.length);
				for (var a:Number = 0; a < chartArray.length; a++ )
				{
					individualChart = chartArray[a] as Dictionary;
					
					kpiName =  individualChart["KPI_Name"] as String;
					category = individualChart["Category"] as Array;
					
					setOccurance = a;
					
					drawGridRow(a, kpiName, category);
					
					
				}
			}
			catch (er:Error)
			{
				Alert.show("[ERROR]"+er.getStackTrace());
			}
			
		}
		
		private function drawGridRow(occurranceIndex:Number, kpi:String,cat:Array):void
		{
			// grid Line horiz
			this.GridRow = new Sprite()
			this.GridRow.graphics.beginFill(0x000000);
			this.GridRow.graphics.drawRect(75, 20 * (occurranceIndex +1), 295, 1);
			this.GridRow.graphics.endFill();
			
			// grid Line Vert
			
			this.kCharts.push(GridRow);
			//this.addChild(this.kCharts[occurranceIndex]);
			//this.addChild(GridRow);
			
			testFormat = new TextFormat("spVerdana",9,0x000000);
			testFormat2 = new TextFormat("Verdana",9, 0x000000);
			
			// text
			test = new TextField();
			test.defaultTextFormat = testFormat;
			test.embedFonts = true;
			test.text = "Evans";
			test.x = 20;
			test.y = 20;// * (occurranceIndex +1);
			test.rotation = 135;
			//addChild(test);
			
			test2 = new TextField();
			test2.text = "Empty";
			
			var test3:TextField = new TextField();
			var b:Number = 0;
			var sf:Boolean = false;
			
			
			if (kpi != null)
			{
				
				if (kpi == "Optimizer_Savings")
				{
					test3 = new TextField();
					test3.x = 0;
					test3.y = 20 * (occurranceIndex +1);
					test3.text = "PLO Savings";
					test3.setTextFormat(testFormat2);
					this.addChild(test3);
			
					for (b = 0; b <= 7; b++ )
					{
						//Alert.show("cat:" + cat[b] + "*");
						if (cat[b] == "")
						{
							cat[b] = "Empty";
						}
						
						if ((cat[b] != null)&&(kCounter["PLO_Savings"]==7))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0xAA0000);
								
								if (!sf)
								{
									this.addDownTriangle(0xAA0000, 100 + (b * 45), 20 * (occurranceIndex +1), true);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["PLO_Savings"]++;
							}
							
						}
						
						if ((cat[b] != null)&&(kCounter["PLO_Savings"]==6))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0xAA0000);
								
								if (!sf)
								{
									// Symbol & Color
									this.addUpTriangle(0xAA0000, 100 + (b * 45), 20 * (occurranceIndex +1), true);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["PLO_Savings"]++;
							
							}
						}
						
						if ((cat[b] != null)&&(kCounter["PLO_Savings"]==5))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0xAA0000);
								
								if (!sf)
								{
									// Symbol & Color
									this.addSquare(0xAA0000, 100 +(b*45), 20 * (occurranceIndex +1), true);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["PLO_Savings"]++;
							}
						}
						
						//Alert.show("cat value:"+cat[b]);
						if ((cat[b] != null)&&(kCounter["PLO_Savings"]==4))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0xAA0000);
								
								if (!sf)
								{
									// Symbol & Color
									this.addCircle(0xAA0000, 100 + (b*45), 20 * (occurranceIndex +1), true);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["PLO_Savings"]++;
			
							}
						}
						
						if ((cat[b] != null)&&(kCounter["PLO_Savings"]==3))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0xAA0000);
								
								if (!sf)
								{
									this.addDownTriangle(0xAA0000, 100 + (b * 45), 20 * (occurranceIndex +1),false);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["PLO_Savings"]++;
							}
							
						}
						
						if ((cat[b] != null)&&(kCounter["PLO_Savings"]==2))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0xAA0000);
								
								if (!sf)
								{
									// Symbol & Color
									this.addUpTriangle(0xAA0000, 100 + (b * 45), 20 * (occurranceIndex +1), false);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["PLO_Savings"]++;
							
							}
						}
						
						if ((cat[b] != null)&&(kCounter["PLO_Savings"]==1))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0xAA0000);
								
								if (!sf)
								{
									// Symbol & Color
									this.addSquare(0xAA0000, 100 +(b*45), 20 * (occurranceIndex +1), false);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["PLO_Savings"]++;
							}
						}
						
						//Alert.show("cat value:"+cat[b]);
						if ((cat[b] != null)&&(kCounter["PLO_Savings"]==0))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0xAA0000);
								
								if (!sf)
								{
									// Symbol & Color
									this.addCircle(0xAA0000, 100 + (b*45), 20 * (occurranceIndex +1),false);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["PLO_Savings"]++;
			
							}
						}
					}
				}
				
				
				if (kpi == "Optimizer_O2")
				{
					
					test3 = new TextField();
					test3.x = 0;
					test3.y = 20 * (occurranceIndex +1);
					test3.text = "O2 Score";
					test3.setTextFormat(testFormat2);
					this.addChild(test3);
			
					for (b = 0; b <= 7; b++ )
					{
						if (cat[b] == "")
						{
							cat[b] = "Empty";
						}
						
						if ((cat[b] != null)&&(kCounter["O2_Score"]==7))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0x00AA00);
								
								if (!sf)
								{
									// Symbol & Color
									this.addDownTriangle(0x00AA00, 100 + (b * 45), 20 * (occurranceIndex +1),true)
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["O2_Score"]++;
							}
						}
						
						if ((cat[b] != null)&&(kCounter["O2_Score"]==6))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0x00AA00);
								
								if (!sf)
								{
									// Symbol & Color
									this.addUpTriangle(0x00AA00, 100 + (b * 45), 20 * (occurranceIndex +1), true);
								}
								
								this.checkCategoryLabel(cat[b], b);
									
								kCounter["O2_Score"]++;
							}
						}
						
						if ((cat[b] != null)&&(kCounter["O2_Score"]==5))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0x00AA00);
								
								if (!sf)
								{
									// Symbol & Color
									this.addSquare(0x00AA00,100 + (b * 45),20 * (occurranceIndex +1), true);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["O2_Score"]++;
							}
						}

						//Alert.show("cat value:"+cat[b]);
						if ((cat[b] != null)&&(kCounter["O2_Score"]==4))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0x00AA00);
								
								if (!sf)
								{
									// Symbol & Color
									this.addCircle(0x00AA00, 100 + (b * 45), 20 * (occurranceIndex +1), true);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["O2_Score"]++;
							}
						}
						
						if ((cat[b] != null)&&(kCounter["O2_Score"]==3))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0x00AA00);
								
								if (!sf)
								{
									// Symbol & Color
									this.addDownTriangle(0x00AA00, 100 + (b * 45), 20 * (occurranceIndex +1), false);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["O2_Score"]++;
							}
						}
						
						if ((cat[b] != null)&&(kCounter["O2_Score"]==2))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0x00AA00);
								
								if (!sf)
								{
									// Symbol & Color
									this.addUpTriangle(0x00AA00, 100 + (b * 45), 20 * (occurranceIndex +1), false);
								}
								
								this.checkCategoryLabel(cat[b], b);
									
								kCounter["O2_Score"]++;
							}
						}
						
						if ((cat[b] != null)&&(kCounter["O2_Score"]==1))
						{
							if (cat[b] != "Empty")
							{
								
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0x00AA00);
								
								if (!sf)
								{
									// Symbol & Color
									this.addSquare(0x00AA00,100 + (b * 45),20 * (occurranceIndex +1), false);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["O2_Score"]++;
							}
						}

						//Alert.show("cat value:"+cat[b]);
						if ((cat[b] != null)&&(kCounter["O2_Score"]==0))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0x00AA00);
								
								if (!sf)
								{
									// Symbol & Color
									this.addCircle(0x00AA00, 100 + (b * 45), 20 * (occurranceIndex +1), false);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["O2_Score"]++;
							}
						}
					}
				}
				
				if (kpi == "GEI_O2")
				{
					
					test3 = new TextField();
					test3.x = 0;
					test3.y = 20 * (occurranceIndex +1);
					test3.text = "GEI";
					test3.setTextFormat(testFormat2);
					this.addChild(test3);
			
					for (b = 0; b <= 7; b++ )
					{
						if (cat[b] == "")
						{
							cat[b] = "Empty";
						}
						
						if ((cat[b] != null)&&(kCounter["GEI"]==7))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0xFF6103);
								
								if (!sf)
								{
									// Symbol & Color
									this.addDownTriangle(0xFF6103, 100 + (b * 45), 20 * (occurranceIndex +1), true);
								}
								
								this.checkCategoryLabel(cat[b],b);
									
								kCounter["GEI"]++;
							}
						}
						
						if ((cat[b] != null)&&(kCounter["GEI"]==6))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0xFF6103);
								
								if (!sf)
								{
									// Symbol & Color
									this.addUpTriangle(0xFF6103, 100 + (b * 45), 20 * (occurranceIndex +1), true);
								}
								
								this.checkCategoryLabel(cat[b],b);
								
								kCounter["GEI"]++;
							}
						}
						
						if ((cat[b] != null)&&(kCounter["GEI"]==5))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0xFF6103);
								
								if (!sf)
								{
									// Symbol & Color
									this.addSquare(0xFF6103,100+ (b * 45),20 * (occurranceIndex +1), true);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["GEI"]++;
							}
						}
						
						if ((cat[b] != null)&&(kCounter["GEI"]==4))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0xFF6103);
								
								if (!sf)
								{
									// Symbol & Color
									this.addCircle(0xFF6103, 100 + (b * 45), 20 * (occurranceIndex +1), true);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["GEI"]++;
							}
						}
						
						if ((cat[b] != null)&&(kCounter["GEI"]==3))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0xFF6103);
								
								if (!sf)
								{
									// Symbol & Color
									this.addDownTriangle(0xFF6103, 100 + (b * 45), 20 * (occurranceIndex +1), false);
								}
								
								this.checkCategoryLabel(cat[b],b);
									
								kCounter["GEI"]++;
							}
						}
						
						if ((cat[b] != null)&&(kCounter["GEI"]==2))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0xFF6103);
								
								if (!sf)
								{
									// Symbol & Color
									this.addUpTriangle(0xFF6103, 100 + (b * 45), 20 * (occurranceIndex +1), false);
								}
								
								this.checkCategoryLabel(cat[b],b);
								
								kCounter["GEI"]++;
							}
						}
						
						if ((cat[b] != null)&&(kCounter["GEI"]==1))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0xFF6103);
								
								if (!sf)
								{
									// Symbol & Color
									this.addSquare(0xFF6103,100+ (b * 45),20 * (occurranceIndex +1), false);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["GEI"]++;
							}
						}
						
						if ((cat[b] != null)&&(kCounter["GEI"]==0))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0xFF6103);
								
								if (!sf)
								{
									// Symbol & Color
									this.addCircle(0xFF6103, 100 + (b * 45), 20 * (occurranceIndex +1), false);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["GEI"]++;
							}
						}
					}
				}
				
				if (kpi == "UnPack_N2")
				{
					
					test3 = new TextField();
					test3.x = 0;
					test3.y = 20 * (occurranceIndex +1);
					test3.text = "N2 UnPack";
					test3.setTextFormat(testFormat2);
					this.addChild(test3);
					//Alert.show("cat :"+cat.join());
					for (b = 0; b <= 7; b++ )
					{
						if (cat[b] == "")
						{
							cat[b] = "Empty";
						}
						
						if ((cat[b] != null)&&(kCounter["N2_UnPack"]==7))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0x87CEFA);
								
								if (!sf)
								{
									this.addDownTriangle(0x87CEFA, 100 + (b * 45), 20 * (occurranceIndex +1), true);
								}
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["N2_UnPack"]++;
							}
						}
						
						if ((cat[b] != null)&&(kCounter["N2_UnPack"]==6))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0x87CEFA);
								
								if (!sf)
								{
									// Symbol & Color
									this.addUpTriangle(0x87CEFA, 100 + (b * 45), 20 * (occurranceIndex +1), true);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["N2_UnPack"]++;
							}
						}
						
						if ((cat[b] != null)&&(kCounter["N2_UnPack"]==5))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0x87CEFA);
								
								if (!sf)
								{
									// Symbol & Color
									this.addSquare(0x87CEFA,100+ (b * 45),20 * (occurranceIndex +1), true);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["N2_UnPack"]++;
							}
						}
						
						if ((cat[b] != null)&&(kCounter["N2_UnPack"]==4))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0x87CEFA);
								
								if (!sf)
								{
									// Symbol & Color
									this.addCircle(0x87CEFA, 100 + (b * 45), 20 * (occurranceIndex +1), true);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["N2_UnPack"]++;
							}
						}
						
						if ((cat[b] != null)&&(kCounter["N2_UnPack"]==3))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0x87CEFA);
								
								if (!sf)
								{
									this.addDownTriangle(0x87CEFA, 100 + (b * 45), 20 * (occurranceIndex +1), false);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["N2_UnPack"]++;
							}
						}
						
						if ((cat[b] != null)&&(kCounter["N2_UnPack"]==2))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0x87CEFA);
								
								if (!sf)
								{
									// Symbol & Color
									this.addUpTriangle(0x87CEFA, 100 + (b * 45), 20 * (occurranceIndex +1), false);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["N2_UnPack"]++;
							}
						}
						
						if ((cat[b] != null)&&(kCounter["N2_UnPack"]==1))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0x87CEFA);
								
								if (!sf)
								{
									// Symbol & Color
									this.addSquare(0x87CEFA,100+ (b * 45),20 * (occurranceIndex +1), false);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["N2_UnPack"]++;
							}
						}
						
						if ((cat[b] != null)&&(kCounter["N2_UnPack"]==0))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0x87CEFA);
								
								if (!sf)
								{
									// Symbol & Color
									this.addCircle(0x87CEFA, 100 + (b * 45), 20 * (occurranceIndex +1), false);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["N2_UnPack"]++;
							}
						}
					}
				}
				
				if (kpi == "Mesa_savings")
				{
					
					test3 = new TextField();
					test3.x = 0;
					test3.y = 20 * (occurranceIndex +1);
					test3.text = "VMESA";
					test3.setTextFormat(testFormat2);
					this.addChild(test3);
			
					for (b = 0; b <= 7; b++ )
					{
						if (cat[b] == "")
						{
							cat[b] = "Empty";
						}
						
						if ((cat[b] != null)&&(kCounter["VMESA"]==7))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0xFF0000);
								
								if (!sf)
								{
									// Symbol & Color
									this.addDownTriangle(0xFF0000, 100 + (b * 45), 20 * (occurranceIndex +1), true);
								}
								
								this.checkCategoryLabel(cat[b], b);
							
								kCounter["VMESA"]++;
							}
						}
						
						if ((cat[b] != null)&&(kCounter["VMESA"]==6))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0xFF0000);
								
								if (!sf)
								{
									// Symbol & Color
									this.addUpTriangle(0xFF0000, 100 + (b * 45), 20 * (occurranceIndex +1), true);
								}
								
								this.checkCategoryLabel(cat[b], b);
									
								kCounter["VMESA"]++;
							}
						}
						
						if ((cat[b] != null)&&(kCounter["VMESA"]==5))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0xFF0000);
								
								if (!sf)
								{
									// Symbol & Color
									this.addSquare(0xFF0000,100 + (b*45),20 * (occurranceIndex +1), true);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["VMESA"]++;
							}
						}
						
						if ((cat[b] != null)&&(kCounter["VMESA"]==4))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0xFF0000);
								
								if (!sf)
								{
									// Symbol & Color
									this.addCircle(0xFF0000, 100 + (b*45), 20 * (occurranceIndex +1), true);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["VMESA"]++;
							}
						}
						
						if ((cat[b] != null)&&(kCounter["VMESA"]==3))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0xFF0000);
								
								if (!sf)
								{
									// Symbol & Color
									this.addDownTriangle(0xFF0000, 100 + (b * 45), 20 * (occurranceIndex +1), false);
								}
								
								this.checkCategoryLabel(cat[b], b);
							
								kCounter["VMESA"]++;
							}
						}
						
						if ((cat[b] != null)&&(kCounter["VMESA"]==2))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0xFF0000);
								
								if (!sf)
								{
									// Symbol & Color
									this.addUpTriangle(0xFF0000, 100 + (b * 45), 20 * (occurranceIndex +1), false);
								}
								
								this.checkCategoryLabel(cat[b], b);
									
								kCounter["VMESA"]++;
							}
						}
						
						if ((cat[b] != null)&&(kCounter["VMESA"]==1))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0xFF0000);
								
								if (!sf)
								{
									// Symbol & Color
									this.addSquare(0xFF0000,100 + (b*45),20 * (occurranceIndex +1), false);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["VMESA"]++;
							}
						}
						
						if ((cat[b] != null)&&(kCounter["VMESA"]==0))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0xFF0000);
								
								if (!sf)
								{
									// Symbol & Color
									this.addCircle(0xFF0000, 100 + (b*45), 20 * (occurranceIndex +1), false);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["VMESA"]++;
							}
						}
					}
				}
				
				if (kpi == "Avg_SCE")
				{
					
					test3 = new TextField();
					test3.x = 0;
					test3.y = 20 * (occurranceIndex +1);
					test3.text = "SCE";
					test3.setTextFormat(testFormat2);
					this.addChild(test3);
			
					for (b = 0; b <= 7; b++ )
					{
						if (cat[b] == "")
						{
							cat[b] = "Empty";
						}
						
						if ((cat[b] != null)&&(kCounter["SCE"]==7))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0x00FF00);
								
								if (!sf)
								{
									// Symbol & Color
									this.addDownTriangle(0x00FF00, 100 + (b * 45), 20 * (occurranceIndex +1), true);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["SCE"]++;
							}
						}
						
						if ((cat[b] != null)&&(kCounter["SCE"]==6))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0x00FF00);
								
								if (!sf)
								{
									// Symbol & Color
									this.addUpTriangle(0x00FF00, 100 + (b * 45), 20 * (occurranceIndex +1), true);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["SCE"]++;
							}
						}
						
						if ((cat[b] != null)&&(kCounter["SCE"]==5))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0x00FF00);
								
								if (!sf)
								{
									// Symbol & Color
									this.addSquare(0x00FF00,100 + (b*45),20 * (occurranceIndex +1), true);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["SCE"]++;
							}
						}
						
						if ((cat[b] != null)&&(kCounter["SCE"]==4))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0x00FF00);
								
								if (!sf)
								{
									// Symbol & Color
									this.addCircle(0x00FF00, 100 + (b * 45), 20 * (occurranceIndex +1), true);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["SCE"]++;
							}
						}
						
						if ((cat[b] != null)&&(kCounter["SCE"]==3))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0x00FF00);
								
								if (!sf)
								{
									// Symbol & Color
									this.addDownTriangle(0x00FF00, 100 + (b * 45), 20 * (occurranceIndex +1), false);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["SCE"]++;
							}
						}
						
						if ((cat[b] != null)&&(kCounter["SCE"]==2))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0x00FF00);
								
								if (!sf)
								{
									// Symbol & Color
									this.addUpTriangle(0x00FF00, 100 + (b * 45), 20 * (occurranceIndex +1), false);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["SCE"]++;
							}
						}
						
						if ((cat[b] != null)&&(kCounter["SCE"]==1))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0x00FF00);
								
								if (!sf)
								{
									// Symbol & Color
									this.addSquare(0x00FF00,100 + (b*45),20 * (occurranceIndex +1), false);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["SCE"]++;
							}
						}
						
						if ((cat[b] != null)&&(kCounter["SCE"]==0))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0x00FF00);
								
								if (!sf)
								{
									// Symbol & Color
									this.addCircle(0x00FF00, 100 + (b * 45), 20 * (occurranceIndex +1), false);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["SCE"]++;
							}
						}
					}
				}
				
				if (kpi == "H2_Model")
				{
					
					test3 = new TextField();
					test3.x = 0;
					test3.y = 20 * (occurranceIndex +1);
					test3.text = "H2 Model";
					test3.setTextFormat(testFormat2);
					this.addChild(test3);
			
					for (b = 0; b <= 7; b++ )
					{
						if (cat[b] == "")
						{
							cat[b] = "Empty";
						}
						
						if ((cat[b] != null)&&(kCounter["H2_Model"]==7))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0x0000FF);
								
								if (!sf)
								{
									// Symbol & Color
									this.addDownTriangle(0x0000FF, 100 + (b * 45), 20 * (occurranceIndex +1), true);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["H2_Model"]++;
							}
						}
						
						if ((cat[b] != null)&&(kCounter["H2_Model"]==6))
						{
							if (cat[b] != "Empty" )
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0x0000FF);
								
								if (!sf)
								{
									// Symbol & Color
									this.addUpTriangle(0x0000FF, 100 + (b * 45), 20 * (occurranceIndex +1), true);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["H2_Model"]++;
							}
						}
						
						if ((cat[b] != null)&&(kCounter["H2_Model"]==5))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0x0000FF);
								
								if (!sf)
								{
									// Symbol & Color
									this.addSquare(0x0000FF,100 + (b*45),20 * (occurranceIndex +1), true);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["H2_Model"]++;
							}
						}
						
						if ((cat[b] != null)&&(kCounter["H2_Model"]==4))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0x0000FF);
								
								if (!sf)
								{
									// Symbol & Color
									this.addCircle(0x0000FF, 100+ (b*45), 20 * (occurranceIndex +1), true);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["H2_Model"]++;
							}
						}
						
						if ((cat[b] != null)&&(kCounter["H2_Model"]==3))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0x0000FF);
								
								if (!sf)
								{
									// Symbol & Color
									this.addDownTriangle(0x0000FF, 100 + (b * 45), 20 * (occurranceIndex +1), false);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["H2_Model"]++;
							}
						}
						
						if ((cat[b] != null)&&(kCounter["H2_Model"]==2))
						{
							if (cat[b] != "Empty" )
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0x0000FF);
								
								if (!sf)
								{
									// Symbol & Color
									this.addUpTriangle(0x0000FF, 100 + (b * 45), 20 * (occurranceIndex +1), false);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["H2_Model"]++;
							}
						}
						
						if ((cat[b] != null)&&(kCounter["H2_Model"]==1))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0x0000FF);
								
								if (!sf)
								{
									// Symbol & Color
									this.addSquare(0x0000FF,100 + (b*45),20 * (occurranceIndex +1), false);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["H2_Model"]++;
							}
						}
						
						if ((cat[b] != null)&&(kCounter["H2_Model"]==0))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0x0000FF);
								
								if (!sf)
								{
									// Symbol & Color
									this.addCircle(0x0000FF, 100+ (b*45), 20 * (occurranceIndex +1), false);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["H2_Model"]++;
							}
						}
					}
				}
				
				if (kpi == "Excess_Steam")
				{
					
					test3 = new TextField();
					test3.x = 0;
					test3.y = 20 * (occurranceIndex +1);
					test3.text = "Excess Steam";
					test3.setTextFormat(testFormat2);
					this.addChild(test3);
			
					for (b = 0; b <= 7; b++ )
					{
						if (cat[b] == "")
						{
							cat[b] = "Empty";
						}
						
						if ((cat[b] != null)&&(kCounter["Excess_Steam"]==7))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0x0B0B0B);
								
								if (!sf)
								{
									// Symbol & Color
									this.addDownTriangle(0x0B0B0B, 100 + (b * 45), 20 * (occurranceIndex +1), true);
								}
								
								this.checkCategoryLabel(cat[b], b);
									
								kCounter["Excess_Steam"]++;
							}
						}
						
						if ((cat[b] != null)&&(kCounter["Excess_Steam"]==6))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0x0B0B0B);
								
								if (!sf)
								{
									// Symbol & Color
									this.addUpTriangle(0x0B0B0B, 100 + (b * 45), 20 * (occurranceIndex +1), true);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["Excess_Steam"]++;
							}
						}
						
						if ((cat[b] != null)&&(kCounter["Excess_Steam"]==5))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0x0B0B0B);
								
								if (!sf)
								{
									// Symbol & Color
									this.addSquare(0x0B0B0B,100 + (b*45),20 * (occurranceIndex +1), true);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["Excess_Steam"]++;
							}
						}
						
						if ((cat[b] != null)&&(kCounter["Excess_Steam"]==4))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0x0B0B0B);
								
								if (!sf)
								{
									// Symbol & Color
									this.addCircle(0x0B0B0B, 100+ (b*45), 20 * (occurranceIndex +1), true);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["Excess_Steam"]++;
							}
						}
						
						if ((cat[b] != null)&&(kCounter["Excess_Steam"]==3))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0x0B0B0B);
								
								if (!sf)
								{
									// Symbol & Color
									this.addDownTriangle(0x0B0B0B, 100 + (b * 45), 20 * (occurranceIndex +1), false);
								}
								
								this.checkCategoryLabel(cat[b], b);
									
								kCounter["Excess_Steam"]++;
							}
						}
						
						if ((cat[b] != null)&&(kCounter["Excess_Steam"]==2))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0x0B0B0B);
								
								if (!sf)
								{
									// Symbol & Color
									this.addUpTriangle(0x0B0B0B, 100 + (b * 45), 20 * (occurranceIndex +1), false);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["Excess_Steam"]++;
							}
						}
						
						if ((cat[b] != null)&&(kCounter["Excess_Steam"]==1))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0x0B0B0B);
								
								if (!sf)
								{
									// Symbol & Color
									this.addSquare(0x0B0B0B,100 + (b*45),20 * (occurranceIndex +1), false);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["Excess_Steam"]++;
							}
						}
						
						if ((cat[b] != null)&&(kCounter["Excess_Steam"]==0))
						{
							if (cat[b] != "Empty")
							{
								sf = this.setSymbolByCategory(cat[b],b,occurranceIndex,0x0B0B0B);
								
								if (!sf)
								{
									// Symbol & Color
									this.addCircle(0x0B0B0B, 100+ (b*45), 20 * (occurranceIndex +1), false);
								}
								
								this.checkCategoryLabel(cat[b], b);
								
								kCounter["Excess_Steam"]++;
							}
						}
					}
				}
			}
		}
		
		private function addCircle(color:uint, x:Number, y:Number, usingSymbolBorder:Boolean):void
		{
			if (usingSymbolBorder)
			{
				this.Circle = new Sprite();
				this.Circle.graphics.beginFill(0xFBE870);
				this.Circle.graphics.drawCircle(x,y+10, 10);
				this.Circle.graphics.endFill();
				this.addChild(Circle);
			}
			
			this.Circle = new Sprite();
			this.Circle.graphics.beginFill(color);
			this.Circle.graphics.drawCircle(x,y+10, 7.5);
			this.Circle.graphics.endFill();
			this.addChild(Circle);
					
		}
		
		private function addSquare(color:uint, x:Number, y:Number, usingSymbolBorder:Boolean):void
		{
			if (usingSymbolBorder)
			{
				this.Square = new Sprite();
				this.Square.graphics.beginFill(0xFBE870);
				this.Square.graphics.drawRect(x-11,y, 20, 20);
				this.Square.graphics.endFill();
				this.addChild(Square);
			}
			
			this.Square = new Sprite();
			this.Square.graphics.beginFill(color);
			this.Square.graphics.drawRect(x-8,y+3, 15, 15);
			this.Square.graphics.endFill();
			this.addChild(Square);
					
		}
		
		private function addDownTriangle(color:uint, x:Number, y:Number, usingSymbolBorder:Boolean):void
		{
			if (usingSymbolBorder)
			{
				this.TriangleDown = new Shape();
				this.TriangleDown.graphics.lineStyle(1, 0xFBE870);
				this.TriangleDown.graphics.beginFill(0xFBE870);
				this.TriangleDown.graphics.moveTo(x+1, y+20);
				this.TriangleDown.graphics.lineTo(x + 10, y);
				this.TriangleDown.graphics.lineTo(x - 10, y);
				this.addChild(TriangleDown);
			}
			
			this.TriangleDown = new Shape();
			this.TriangleDown.graphics.lineStyle(1, color);
			this.TriangleDown.graphics.beginFill(color);
			this.TriangleDown.graphics.moveTo(x+1, y+15);
			this.TriangleDown.graphics.lineTo(x + 7.5, y);
			this.TriangleDown.graphics.lineTo(x - 7.5, y);
			this.addChild(TriangleDown);
		}
		
		private function addUpTriangle(color:uint, x:Number, y:Number, useSymbolBorder:Boolean):void
		{
			if (useSymbolBorder)
			{
				this.TriangleUp = new Shape();
				this.TriangleUp.graphics.lineStyle(1, 0xFBE870);
				this.TriangleUp.graphics.beginFill(0xFBE870);
				this.TriangleUp.graphics.moveTo(x, y-1);
				this.TriangleUp.graphics.lineTo(x + 12, 20 + y);
				this.TriangleUp.graphics.lineTo(x - 12, 20 + y);
				this.addChild(TriangleUp);
			}
			
			this.TriangleUp = new Shape();
			this.TriangleUp.graphics.lineStyle(1, color);
			this.TriangleUp.graphics.beginFill(color);
			this.TriangleUp.graphics.moveTo(x, y+3);
			this.TriangleUp.graphics.lineTo(x + 7.5, 16 + y);
			this.TriangleUp.graphics.lineTo(x - 7.5, 16 + y);
			this.addChild(TriangleUp);
		}
		
		private function checkCategoryLabel(cat:String, indx:Number):void
		{
			//Alert.show("category:"+cat+"-indexOf:"+cat.indexOf(test2.text)+"-index:"+indx);
			if (cat.indexOf(test2.text) < 0)
			{
				test2 = new TextField();
				test2.defaultTextFormat = testFormat;
				test2.embedFonts = true;
				//test2.setTextFormat(testFormat2);
				test2.x = 85 + (indx*45);
				test2.y = 5;
				
				if (cat.indexOf("Shift") < 0)
				{
					if (cat.indexOf("_") >= 0)
					{
						test2.text = cat.split("_")[1];
					}
					else
					{
						test2.text = cat;
					}
				}
				else
				{
					if ((cat.indexOf("Shift") >= 0) && (cat.indexOf("_") ))
					{
						test2.text = cat.split("_")[0];
					}
					else
					{
						test2.text = cat;
					}
				}
				
				if (!printedCategory[test2.text])
				{
					this.addChild(test2);
				}
				
				printedCategory[test2.text] = true;
			}
		}
		
		private function setSymbolByCategory(cat:String, b:Number, oi:Number,color:uint):Boolean
		{
			//Alert.show("Cat:" + cat);
			var symbolSet:Boolean = false;
			
			if (cat.indexOf("Evans") >= 0)
			{
				symbolSet = true;
				this.addCircle(color, 100+ (b*45), 20 * (oi +1), false);			
			}
				
			if(cat.indexOf("George") >= 0 )
			{
				symbolSet = true;
				this.addSquare(color, 100+ (b*45), 20 * (oi +1), false);				
			}
				
			if (cat.indexOf("Stanford") >= 0)
			{
				symbolSet = true;
				this.addUpTriangle(color, 100 + (b * 45), 20 * (oi +1), false);	
				//lert.show("Here");
			}
				
			if (cat.indexOf("Adams") >= 0)
			{
				symbolSet = true;
				this.addDownTriangle(color, 100+ (b*45), 20 * (oi +1), false);				
			}
				
			if (cat.indexOf("Fox") >= 0)
			{
				symbolSet = true;
				this.addCircle(color, 100+ (b*45), 20 * (oi +1), true);				
			}
				
			if (cat.indexOf("Salinas") >= 0)
			{
				symbolSet = true;
				this.addSquare(color, 100+ (b*45), 20 * (oi +1), true);				
			}
				
			if (cat.indexOf("Roberts") >= 0)
			{
				symbolSet = true;
				this.addUpTriangle(color, 100+ (b*45), 20 * (oi +1), true);						
			}
				
			if (cat.indexOf("John") >= 0)
			{
				symbolSet = true;
				this.addDownTriangle(color, 100+ (b*45), 20 * (oi +1), true);						
			}
				
			return symbolSet;
		}
	}
}