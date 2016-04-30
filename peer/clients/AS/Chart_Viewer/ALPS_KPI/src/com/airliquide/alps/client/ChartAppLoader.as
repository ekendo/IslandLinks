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
	 * ...
	 * @author earl.lawrence
	 */
	public class ChartAppLoader
	{
		private var viewer:ChartViewer;
		private var viewerClass:Class;
		private var chartValues:Map;
		private var chartData:Map;
		private var categoryValues:Array;
		private var fieldValues:Array;
		private var dataKeys:Array;
		private var dataKeyValues:Array;
		
		private var loaderInfo:Object;
		
		// debug
		private var txtTest:Label = new Label();
		
		public function ChartAppLoader() 
		{
			// load up the chart Vals
			this.chartValues = new Map();
			this.chartData = new Map();
			
			txtTest.text = "\n\r\n\r\n\r\n\r";
			txtTest.x = 0;
			txtTest.y = 100;
			txtTest.width = 1000;
			txtTest.height = 100;
			
		}
		
		public function GetDebugTxt():String 
		{
			return this.txtTest.text;
			//return "test";
		}
		
		public function GetChartValues():Map
		{
			return this.chartValues;
		}
		
		public function GetChartData():Map 
		{
			return this.chartData;
		}
		
		public function GetCategoryValues():Array
		{
			return this.categoryValues;
		}
		
		public function GetFieldValues():Array 
		{
			return this.fieldValues;
		}
		
		public function GetChartDataKeys():Array
		{
			return this.dataKeys;
		}
		
		public function GetChartDataKeyValues():Array
		{
			return this.dataKeyValues;
		}
		
		public function GetVariables(lInfo:Object):void 
		{
			this.loaderInfo = lInfo;
			
			
			// get chart stuffs
			if (loaderInfo.parameters.chartRenderrer != null)
			{
				this.chartValues.setValue("Renderrer",loaderInfo.parameters.chartRenderrer);
				//txtTest.text = "chartRenderer ain null";
			}
			else 
			{
				//txtTest.text = "chartRenderer is null";
			}
			
			if (loaderInfo.parameters.chartDisplayFormat != null)
			{
				this.chartValues.setValue("DisplayFormat",loaderInfo.parameters.chartDisplayFormat);
				//txtTest.text = "chartDisplayFormat ain null";
			}
			else 
			{
				//txtTest.text = "chartDisplayFormat is null";
			}
			
			if (loaderInfo.parameters.chartDateSubKeyIndex != null)
			{
				this.chartValues.setValue("DateSubKeyIndex",loaderInfo.parameters.chartDateSubKeyIndex);
				//txtTest.text = "chartDisplayFormat ain null";
			}
			else 
			{
				//txtTest.text = "chartDisplayFormat is null";
			}
			
			if (loaderInfo.parameters.chartMetricAliasKeyIndex != null)
			{
				this.chartValues.setValue("MetricAliasKeyIndex",loaderInfo.parameters.chartMetricAliasKeyIndex);
				//txtTest.text = "chartDisplayFormat ain null";
			}
			else 
			{
				//txtTest.text = "chartDisplayFormat is null";
			}
			
			if (loaderInfo.parameters.chartMetricAliasKeys != null)
			{
				this.chartValues.setValue("MetricAliasKeys",loaderInfo.parameters.chartMetricAliasKeys);
				//txtTest.text = "chartDisplayFormat ain null";
			}
			else 
			{
				//txtTest.text = "chartDisplayFormat is null";
			}
			
			
			if (loaderInfo.parameters.chartDataAlwaysIncludesMonths != null)
			{
				this.chartValues.setValue("DataAlwaysIncludesMonths",loaderInfo.parameters.chartDataAlwaysIncludesMonths);
				//txtTest.text = "chartDisplayFormat ain null";
			}
			else 
			{
				//txtTest.text = "chartDisplayFormat is null";
			}
			
			if (loaderInfo.parameters.chartCategories != null)
			{
				this.chartValues.setValue("Categories", loaderInfo.parameters.chartCategories);
				this.categoryValues = chartValues.getValue("Categories").split(",");
				//txtTest.text = "chartCategories ain null";
			}
			else 
			{
				//txtTest.text = "chartCategories is null";
			}
			
			
			if (loaderInfo.parameters.chartFields != null)
			{
				this.chartValues.setValue("Fields",loaderInfo.parameters.chartFields);
				//txtTest.text = "chartFields ain null";
			}
			else 
			{
				//txtTest.text = "chartFields is null";
			}
			
			
			if (loaderInfo.parameters!=null)
			{
				this.chartValues.setValue("DataType", "HTML");
				//txtTest.text = "we definately had parameters";
			}
			else
			{
				//txtTest.text = "we had no parameters";
			}
			
			
			// get field stuffs
			if (this.chartValues.length() > 0)
			{
				// get Field values
				this.getFieldValues(this.chartValues)
			
				//txtTest.text += "called getFieldValues";
				
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
			var dataSubKeyValueElements:Array;
			var chartDataKey:String;
			var chartDataValues:String;
			var chartDataValueKeyName:String = "";
			
			// generate all the keys first
			for (i = 0; i < this.categoryValues.length;i++ )
			{
				if (this.categoryValues[i] != null)
				{
					// create the full keys
					if((this.categoryValues[i].indexOf("Chart_By")>=0)||(this.categoryValues[i].indexOf("Filter_By")>=0))
					{
						// loop through the chart fields => Operator
						//for (j = 0; j < this.dataKeys.length; j++ )
						{
							if (i == 0)
							{
								// 1st elements
								dataKeyValueElements = this.dataKeyValues[i].split(",");
								dataSubKeyValueElements = this.dataKeyValues[i].split(",");
								//this.txtTest.appendText("*:"+dataKeyValueElements[0]+"*");
								
								if(this.chartValues.getValue("MetricAliasKeyIndex")!=null)
								{
									var metricAliasKeyIndex:Number = new Number(this.chartValues.getValue("MetricAliasKeyIndex"));
									
									if (metricAliasKeyIndex == 0)
									{
										var dataMetricKeyValueElements:Array = this.chartValues.getValue("MetricAliasKeys").split(",");
										
										// add em to the data and sub key arrays
										for (var a:Number=0; a < dataMetricKeyValueElements.length;a++ )
										{
											dataKeyValueElements.push(dataMetricKeyValueElements[a]);
											dataSubKeyValueElements.push(dataMetricKeyValueElements[a]);
										}
									}
								}
							}
							else
							{
								var tmp:Array = dataKeyValues[i].split(",");
								var tmp2:Array = new Array();
								var tmp3:Array = new Array();
								//this.txtTest.appendText(">:"+dataKeyValues[j]+"<");
								
								for (k = 0; k < dataKeyValueElements.length;k++ )
								{
									var s:String = dataKeyValueElements[k];
									var t:String = dataKeyValueElements[k];
									//this.txtTest.appendText("dkve:"+dataKeyValueElements[k]);
									
									for (var m:Number = 0; m < tmp.length;m++ )
									{
										try
										{
											//this.txtTest.text="{" + "before bool" + "}";
											if(this.chartValues.getValue("DataAlwaysIncludesMonths")!=null)
											{
												//this.txtTest.text="{" + "bool is not null" + "}";
												if(this.chartValues.getValue("DataAlwaysIncludesMonths")=="false")
												{
													s = dataKeyValueElements[k] + "_" + tmp[m];
													
													switch(tmp[m])
													{
														case "January":
															t = dataKeyValueElements[k] + "_Month_Value";
															break;
														case "February":
															t = dataKeyValueElements[k] + "_Month_Value";
															break;
														case "March":
															t = dataKeyValueElements[k] + "_Month_Value";
															break;
														case "April":
															t = dataKeyValueElements[k] + "_Month_Value";
															break;
														case "May":
															t = dataKeyValueElements[k] + "_Month_Value";
															break;
														case "June":
															t = dataKeyValueElements[k] + "_Month_Value";
															break;
														case "July":
															t = dataKeyValueElements[k] + "_Month_Value";
															break;
														case "August":
															t = dataKeyValueElements[k] + "_Month_Value";
															break;
														case "September":
															t = dataKeyValueElements[k] + "_Month_Value";
															break;
														case "October":
															t = dataKeyValueElements[k] + "_Month_Value";
															break;
														case "November":
															t = dataKeyValueElements[k] + "_Month_Value";
															break;
														default: 
															t = dataKeyValueElements[k] + "_" + tmp[m];
															break;
													}
													//this.txtTest.text+="{" + s + "}";
												}
												else
												{
													s = dataKeyValueElements[k] + "_" + tmp[m];
													t = dataKeyValueElements[k] + "_" + tmp[m];
												}
											}
											else
											{
												s = dataKeyValueElements[k] + "_" + tmp[m];
												t = dataKeyValueElements[k] + "_" + tmp[m];
											}
											
											//this.txtTest.text += "{" + s + "}\n";
											tmp2.push(s);
											//break;
											if (t != s)
											{
												tmp2.push(t);
											}
											
											
											if(this.chartValues.getValue("DateSubKeyIndex")!=null)
											{	
												var dateSubKeyIndex:Number = new Number(this.chartValues.getValue("DateSubKeyIndex"));
												
												if (i == dateSubKeyIndex)
												{
													//if(tmp3.indexOf)
													//this.txtTest.text += i;
													tmp3.push(s);
													
													if (t != s)
													{
														tmp3.push(t);
													}
												}
											}
										}
										catch (err:Error)
										{
											this.txtTest.text += "[ERROR]" + err.getStackTrace();
										}
									}
									
									//tmp2.push(s);
									//this.txtTest.text+="{"+s+"}";
									//break;
								}
								
								dataKeyValueElements = tmp2;
								if (tmp3.length > 0)
								{
									dataSubKeyValueElements = tmp3;
									
								}
								//this.txtTest.text+="*:"+dataKeyValueElements[0];
								//this.txtTest.text+="@:"+dataSubKeyValueElements[0];
							}
						
							//this.txtTest.text="*:"+dataKeyValueElements[0];
							//break;
						}	
					}
				}
				
				//this.txtTest.text = dataKeyValueElements[0];
				//break;
			}
			
			//this.txtTest.text = "*";
			
			// now get the chart data by key
			for (j = 0; j < dataKeyValueElements.length; j++ )
			{
				if (dataKeyValueElements[j] != null)
				{
					if ((dataKeyValueElements[j].indexOf("Month_Value") >= 0)&&(dataKeyValueElements[j].indexOf("Opr3") >= 0)&&(dataKeyValueElements[j].indexOf("All")<0))
					{
						//this.txtTest.text += "{?" + dataKeyValueElements[j] + "}\n";
					}
				}
				
				var dataKey:String = dataKeyValueElements[j] + "_Values";
				
				if (loaderInfo.parameters[dataKey] != null)
				{
					this.chartData.setValue(dataKey, this.loaderInfo.parameters[dataKey]);
					//this.txtTest.text += "{Piped:" + dataKey + "}\n";
				}
				
				dataKey = dataKeyValueElements[j] + "_Date_Values";
				
				if (loaderInfo.parameters[dataKey] != null)
				{
					this.chartData.setValue(dataKey, this.loaderInfo.parameters[dataKey]);
					//this.txtTest.text += "{Dates:" + dataKey + "}\n";
				}
				
				if(this.chartValues.getValue("DateSubKeyIndex")!=null)
				{
					dataKey = dataSubKeyValueElements[j] + "_Date_Values";
					
					// create month buckets for later
					if (this.chartValues.getValue("DataAlwaysIncludesMonths") != null)
					{
						
						if (this.chartValues.getValue("DataAlwaysIncludesMonths") == "false")
						{
							if (dataKey.indexOf("Month_Value") >= 0)
							{
								if (this.chartValues.getValue(dataKey) == null)
								{
									// recreate key minus month
									var noMonthKey:String = dataKey.substring(0, dataKey.indexOf("_Month_Value")) + dataKey.substring(dataKey.indexOf("_Month_Value")+12);
									
									if (this.loaderInfo.parameters[noMonthKey] != null)
									{	
										// save no month key and value
										this.chartData.setValue(noMonthKey, this.loaderInfo.parameters[noMonthKey]);
										//this.txtTest.text += "{Lotus:" + noMonthKey + ":";
										
										var allAvailableDates:Array = this.loaderInfo.parameters[noMonthKey].split(",");
										var thisKeyMonth:String = "";
										var thisKeyMonthDates:String = "";
										var thisKeyMonthStartIndex:String = "0";
										var thisKeyMonthEndIndex:String = "";
										var lastKeyMonth:String = "";
										var lastKeyMonthDates:String = "";
										var lastKeyMonthStartIndex:String = "";
										var lastKeyMonthEndIndex:String = "";
										var newMonthKey:String = "";
										var oldMonthKey:String = "";
										var beginIndex:Number = new Number(0);
										var endIndex:Number = new Number(0);
										var indexLength:Number = new Number(0);
										
										//this.txtTest.text += this.loaderInfo.parameters[noMonthKey]+"}\n";
										for (var b:Number = 0; b < allAvailableDates.length;b++ )
										{
											// create actual date
											var actualDate:Date = new Date();
											actualDate.setTime(Date.parse(allAvailableDates[b]));
											
											// get the month
											switch(actualDate.month)
											{
												case 0:
													thisKeyMonth = "January";
													thisKeyMonthDates += actualDate.toDateString() + ",";
													break;
												case 1:
													thisKeyMonth = "February";
													thisKeyMonthDates += actualDate.toDateString() + ",";
													break;
												case 2:
													thisKeyMonth = "March";
													thisKeyMonthDates += actualDate.toDateString() + ",";
													break;
												case 3:
													thisKeyMonth = "April";
													thisKeyMonthDates += actualDate.toDateString() + ",";
													break;
												case 4:
													thisKeyMonth = "May";
													thisKeyMonthDates += actualDate.toDateString() + ",";
													break;
												case 5:
													thisKeyMonth = "June";
													thisKeyMonthDates += actualDate.toDateString() + ",";
													break;
												case 6:
													thisKeyMonth = "July";
													thisKeyMonthDates += actualDate.toDateString() + ",";
													break;
												case 7:
													thisKeyMonth = "August";
													thisKeyMonthDates += actualDate.toDateString() + ",";
													break;
												case 8:
													thisKeyMonth = "September";
													thisKeyMonthDates += actualDate.toDateString() + ",";
													break;
												case 9:
													thisKeyMonth = "October";
													thisKeyMonthDates += actualDate.toDateString() + ",";
													break;
												case 10:
													thisKeyMonth = "November";
													thisKeyMonthDates += actualDate.toDateString() + ",";
													break;
												case 11:
													thisKeyMonth = "December";
													thisKeyMonthDates += actualDate.toDateString() + ",";
													break;
											}
											
											// create key with Month tag 
											newMonthKey =  dataKey.substring(0, dataKey.indexOf("_Month_Value")) + "_" + thisKeyMonth + dataKey.substring(dataKey.indexOf("_Month_Value")+12);
											oldMonthKey =  dataKey.substring(0, dataKey.indexOf("_Month_Value")) + "_" + lastKeyMonth + dataKey.substring(dataKey.indexOf("_Month_Value")+12);
											
											
											if (thisKeyMonth != lastKeyMonth)
											{
												if (this.chartData.getValue(newMonthKey) == null)
												{
													if (lastKeyMonth != "")
													{
														// save old
														if ((this.chartData.getValue(oldMonthKey) != null)&&(lastKeyMonthDates.indexOf(this.chartData.getValue(oldMonthKey))<0))
														{
															lastKeyMonthDates += "," + this.chartData.getValue(oldMonthKey); 
														}
														this.chartData.setValue(oldMonthKey, lastKeyMonthDates);
														if (newMonthKey.indexOf("Opr2") >= 0)
														{
															//this.txtTest.text += "{-Saving Old:" +oldMonthKey + ":" + lastKeyMonthDates + "}";
														}
													}
													
													thisKeyMonthDates = actualDate.toDateString() + ",";
													thisKeyMonthStartIndex = b.toString();
													this.chartData.setValue(newMonthKey, thisKeyMonthDates);
													this.chartData.setValue(newMonthKey + "_Index", thisKeyMonthStartIndex);
													this.chartData.setValue(newMonthKey + "_Length", "0");
													beginIndex = new Number(thisKeyMonthStartIndex);
													
													if (newMonthKey.indexOf("Opr1") >= 0)
													{
														//this.txtTest.text += "{Starting New-"+newMonthKey + ":" + thisKeyMonthDates + "}\n";
													}
												}
												else
												{
													thisKeyMonthDates = actualDate.toDateString() + "," + this.chartData.getValue(newMonthKey);
													
													this.chartData.setValue(newMonthKey, thisKeyMonthDates);
														
													//beginIndex = new Number( this.chartData.getValue(newMonthKey + "_Index"))
													endIndex = new Number( this.chartData.getValue(newMonthKey + "_Length"));
													if (endIndex == 0)
													{
														endIndex++;
													}
													//indexLength = endIndex - beginIndex;
													this.chartData.setValue(newMonthKey + "_Index", this.chartData.getValue(newMonthKey + "_Index") + "," + b.toString());
													this.chartData.setValue(newMonthKey + "_Length", endIndex + ",0");
													
													if (newMonthKey.indexOf("Opr1") >= 0)
													{
														//this.txtTest.text += "{Re-Starting New-"+newMonthKey + ":" + this.chartData.getValue(newMonthKey) +" INDEX:"+this.chartData.getValue(newMonthKey + "_Index")+" LENGTH:"+this.chartData.getValue(newMonthKey + "_Length")+"}\n";
													}
												}
												
												//this.txtTest.text += "{Starting:" + newMonthKey + "_Index" + ":"+this.chartData.getValue(newMonthKey + "_Index")+"}\n";
												
											}
											else
											{
												
												// update existing key
												
												//this.chartData.setValue(newMonthKey, thisKeyMonthDates);
												if (this.chartData.getValue(newMonthKey) != null)
												{
													thisKeyMonthDates= actualDate.toDateString() + "," + this.chartData.getValue(newMonthKey);
												}
												
												this.chartData.setValue(newMonthKey, thisKeyMonthDates);
												
												//thisKeyMonthEndIndex = b.toString();
												//endIndex = b;
												//indexLength = endIndex - beginIndex;
												// update length
												var lengths:Array  = new Array();
												try
												{
													if (this.chartData.getValue(newMonthKey + "_Length") != null)
													{
														if (this.chartData.getValue(newMonthKey + "_Length").indexOf(",") < 0)
														{
															indexLength = new Number( this.chartData.getValue(newMonthKey + "_Length"));
															indexLength++;
															this.chartData.setValue(newMonthKey + "_Length", indexLength.toString());
														}
														else
														{
															lengths = this.chartData.getValue(newMonthKey + "_Length").split(",");
															indexLength = new Number( lengths[lengths.length - 1]);
															indexLength++;
															lengths[lengths.length - 1] = indexLength;
															this.chartData.setValue(newMonthKey + "_Length", lengths.toString());
														}
													}
													else
													{
														indexLength = new Number( this.chartData.getValue(newMonthKey + "_Length"));
														indexLength++;
														this.chartData.setValue(newMonthKey + "_Length", indexLength.toString());
													}
												}
												catch (ex:Error)
												{
													this.txtTest.text += "\n[ERROR] problems setting dates and lengths:"+ex.getStackTrace();
												}
												
												
												if (newMonthKey.indexOf("Opr1") >= 0)
												{
													//this.txtTest.text += "{Updating:" + newMonthKey + ":" + this.chartData.getValue(newMonthKey) +" INDEX:"+this.chartData.getValue(newMonthKey + "_Index")+" LENGTH:"+this.chartData.getValue(newMonthKey + "_Length")+ "}\n";
												}
											}
											
											lastKeyMonth = thisKeyMonth;
											lastKeyMonthDates = thisKeyMonthDates;
											
										}
										
										this.loaderInfo.parameters[noMonthKey] = null;
										
									}
									//this.txtTest.text += "{" + noMonthKey + ":"+j+"}\n";
								}
								//this.txtTest.text = "{*" + dataSubKeyValueElements[j] + "_Date_Values" + ":}";
							}
							//this.txtTest.text = "{" + dataSubKeyValueElements[0] + "_Date_Values" + ":}";
						}
						
					}
				}
				
				dataKey = dataKeyValueElements[j] + "_Metric_Values";
				
				if (dataKey.indexOf("Month_Value") >= 0)
				{
				
					var noMonthMetricKey:String =  dataKey.substring(0, dataKey.indexOf("_Month_Value")) + dataKey.substring(dataKey.indexOf("_Month_Value")+12);
									
					if (loaderInfo.parameters[noMonthMetricKey] != null)
					{
						this.chartData.setValue(noMonthMetricKey, this.loaderInfo.parameters[noMonthMetricKey]);
						//this.txtTest.text += "{Metric:" + noMonthMetricKey + "}\n";
					}
				}
				
				dataKey = dataKeyValueElements[j] + "_Metric_Values";
				
				if (loaderInfo.parameters[dataKey] != null)
				{
					this.chartData.setValue(dataKey, this.loaderInfo.parameters[dataKey]);
					//this.txtTest.text += "{Metric:" + dataKey + "}\n";
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
			//this.txtTest.text = "here at getFildVals";
			
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
		
			//this.txtTest.text = "";
			
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
					
						//this.txtTest.text = this.txtTest.text  + key +":" + value;
					}
				}
			}
		}
		
	}

}