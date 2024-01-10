package com.airliquide.alps.chart.data 
{
	// AS3 SDK
	//import com.adobe.utils.DictionaryUtil;
	import flash.utils.*;
	
	// FLEX SDK 
	import mx.collections.ArrayCollection;
	import mx.controls.Label;
	import mx.controls.Alert;
	import mx.charts.series.BarSeries;
	import mx.charts.series.LineSeries;
	import mx.charts.series.ColumnSeries;
	import mx.charts.series.AreaSeries;
	import mx.formatters.*;
	/**
	 * ...
	 * @author earl.lawrence
	 */
	public class FormatParser
	{
		public static var FROM_COMMA_AND_PIPE_TO_X_AND_Y_ARRAY:int = 0;
		public static var FROM_COMMA_AND_PIPE_TO_X_AND_Y_MAP:int = 1;
		public static var FROM_COMMA_AND_PIPE_TO_X_AND_Y_DICTIONARY:int = 2;
		public static var FROM_COMMA_AND_PIPE_TO_BARSERIES_ARRAY:int = 3;
		public static var FROM_COMMA_AND_PIPE_TO_X_AND_Y_ARRAY_COLLECTION:int = 4;
		public static var FROM_COMMA_AND_PIPE_TO_ARRAY_COLLECTION:int = 5;
		public static var FROM_COMMA_DATE_METRIC_TO_ARRAY_COLLECTION:int = 6;
		public static var FROM_COMMA_DATE_METRIC_TO_X_AND_Y_ARRAY_COLLECTION:int = 7;
		public static var FROM_CURRENT_DATA_TO_X_AND_Y_DICTIONARY:int = 8;
		public static var FROM_CURRENT_DATA_TO_DICTIONARY_ARRAY:int = 9;
		public static var FROM_CURRENT_DATA_TO_BARSERIES_ARRAY:int = 10;
		public static var FROM_CURRENT_DATA_TO_LINESERIES_ARRAY:int = 11;
		public static var FROM_CURRENT_DATA_TO_COLUMNSERIES_ARRAY:int = 12;
		public static var FROM_CURRENT_DATA_TO_AREASERIES_ARRAY:int = 13;
		public static var FROM_CURRENT_DATA_TO_ARRAY_COLLECTION:int = 14;
		public static var FROM_ARRAY_COLLECTION_TO_LINESERIES_ARRAY:int = 15;
		public static var FROM_ARRAY_COLLECTION_TO_BARSERIES_ARRAY:int = 16;
		public static var FROM_ARRAY_COLLECTION_TO_AREASERIES_ARRAY:int = 17;
		
		private var dataInput:Object;
		private var dateDataInput:Object;
		private var valueDataInput:Object;
		private var parseResults:Object;
		private var kpiMaxValues:Array;
		private var chartArrayValues:Array;
		private var lastParseFormat:int;
		private var lastlineIndex:int = 0;
		private var sortField:String;
		private var currentKpiName:String;
		private var currentCategory:Array;
		private var dictDataArrayCount:Number;
		
		// debug text
		private var dbgText:Label;
		
		/* Constructor */
		public function FormatParser() 
		{
			this.dbgText = new Label();
		}
		
		/* Get */
		public function GetDebugText():String 
		{
			return this.dbgText.text;
		}
		
		/* Set */
		public function SetAggregationData(data:Array):void
		{
			dataInput = data;
		}
		
		public function SetTotalDataSet(dates:Array, values:Array):void
		{
			
				this.dateDataInput = dates;
				this.valueDataInput = values;
		}
		
		public function SetKpiMaxConstants(c:Array):void
		{
			kpiMaxValues = c;
		}
		
		public function SetDataChartArray(ca:Array):void
		{
			chartArrayValues = ca;
		}
		
		public function ParseDataChartArray(dt:String,method:int):Object
		{
			switch(method)
			{
				case FROM_COMMA_DATE_METRIC_TO_X_AND_Y_ARRAY_COLLECTION:
					var kpiMaxPossible:Number = 0;
					var chartResults:ArrayCollection = new ArrayCollection();
					var dictData:Dictionary = new Dictionary();
					var dictDataArray:Array = new Array();
					var comboDictData:Dictionary = new Dictionary();
					var chartResult:ArrayCollection;
					var dateMatch:Boolean = false;
					var matchCnt:Number = 0;
					var num:Number = 0;
					
					try 
					{
						if (dt == "AggregatedChart")
						{
							
							// get alll the chart data
							for (var a:Number = 0; a < this.chartArrayValues.length; a++ )
							{
								dictData = this.chartArrayValues[a] as Dictionary;
								
								//kpiMaxPossible += dictData["KPI_MaxData"] as Number;
								this.kpiMaxValues = dictData["KPI_MaxData"];
								this.currentKpiName = dictData["KPI_Name"];
								//this.currentCategory = dictData["Y_Label"];
								//Alert.show(currentCategory);
								
								
								//Alert.show("chartDataString="+dictData["ChartStringData"]);
								if (dictData["ChartStringData"].length >0)
								{
									chartResult = ParseDataString(dictData["ChartStringData"] as String, FormatParser.FROM_COMMA_DATE_METRIC_TO_X_AND_Y_ARRAY_COLLECTION, dictData["X_Label"] as String, dictData["Y_Label"] as String) as ArrayCollection;
									//Alert.show("data!"+chartResults.length+","+chartResult.length+":"+this.currentKpiName+":cRes-"+chartResult);
									
									// flatten dataset
									if (chartResults.length==0)
									{
										if (chartResult != null)
										{
											// add the date placeholders
											for (var b:Number = 0; b < chartResult.length; b++ )
											{
												comboDictData = new Dictionary();
												
												comboDictData[dictData["Y_Label"]] = chartResult[b][dictData["Y_Label"]];
												if ((comboDictData[dictData["Y_Label"]].indexOf("Day_Shift") < 0) && (comboDictData[dictData["Y_Label"]].indexOf("Night_Shift") < 0))
												{
													comboDictData[dictData["Y_Label"]] = comboDictData[dictData["Y_Label"]].substring(comboDictData[dictData["Y_Label"]].indexOf("_")+1);
												}
												comboDictData[a.toString()] = chartResult[b][dictData["X_Label"]];
												num = new Number(chartResult[b]["total_max"] - chartResult[b][dictData["X_Label"]]);
												comboDictData["max"] = new Number(num.toPrecision(3));
												//comboDictData["Name"] = dictData["KPI_Name"];
												//Alert.show("indx:"+a+"-score:"+chartResult[b][dictData["X_Label"]]);
												//Alert.show("Max0:"+chartResult[b]["total_max"]+"<-->Value:"+chartResult[b][dictData["X_Label"]]);
												chartResults.addItem(comboDictData);
												//Alert.show("this is a="+a+":"+chartResult[b][dictData["Y_Label"]]);
											}
										}
									}
									else
									{
										for (var z:Number = 0; z< chartResult.length;z++)
										{
											dateMatch = false;
												
											for (var y:Number = 0; y< chartResults.length;y++)
											{
												
												if (chartResult[z][dictData["Y_Label"]] == chartResults[y][dictData["Y_Label"]])
												{
														dateMatch = true;
												}
											}
											
											if (!dateMatch)
											{
												//Alert.show("Missed Date:" + chartResult[z][dictData["Y_Label"]]+":"+chartResult[z]["total_max"]+":"+chartResult[z]["max"]);
												comboDictData = new Dictionary();
												
												comboDictData[dictData["Y_Label"]] = chartResult[z][dictData["Y_Label"]];
												if ((comboDictData[dictData["Y_Label"]].indexOf("Day_Shift") < 0) && (comboDictData[dictData["Y_Label"]].indexOf("Night_Shift") < 0))
												{
													comboDictData[dictData["Y_Label"]] = comboDictData[dictData["Y_Label"]].substring(comboDictData[dictData["Y_Label"]].indexOf("_")+1);
												}
												comboDictData[a.toString()] = chartResult[z][dictData["X_Label"]];
												comboDictData["total_max"] = chartResult[z]["total_max"];
												//num = new Number(chartResult[z]["total_max"] - chartResult[z][dictData["X_Label"]]);
												//comboDictData["max"] = new Number(num.toPrecision(3));
												//comboDictData["max"] = chartResult[z]["max"];
												comboDictData["max"] = 0;
												
												//Alert.show("Max"+a+":"+chartResult[z]["total_max"]+"<-->Value:"+chartResult[z][dictData["X_Label"]]);
														
												// add it
												chartResults.addItem(comboDictData);
												
											}
										}
										
										{
											for (var c:Number = 0; c < chartResults.length; c++ )
											{
												// initialize
												chartResults[c][a.toString()] = new Number(0);
												//dateMatch = false;
												
												// find the date match
												for (var d:Number = 0; d < chartResult.length; d++ )
												{
													
													//Alert.show("chartResult val="+chartResult[d][dictData["Y_Label"]]+" and dictData is=" + chartResults[c][dictData["Y_Label"]]);
													if (chartResult[d][dictData["Y_Label"]] == chartResults[c][dictData["Y_Label"]])
													{
														//Alert.show("Date Match!!");
														chartResults[c][a.toString()] = chartResult[d][dictData["X_Label"]];
														num = new Number( chartResult[d]["total_max"] - chartResult[d][dictData["X_Label"]] ) + chartResults[c]["max"];
														chartResults[c]["max"] = new Number( num.toPrecision(3));
														//chartResults[c]["total_max"] = chartResult[d]["total_max"];
														//chartResults[c]["Name"] = dictData["KPI_Name"];
														//Alert.show("Max"+a+":"+chartResult[d]["total_max"]+"<-->Value:"+chartResult[d][dictData["X_Label"]]);
														//Alert.show("this is a="+a+":"+chartResults[c][dictData["Y_Label"]]);
													}
												}
												
												//Alert.show("These Are the results:"+);
											}
										}
									}
									
									//Alert.show(chartResults.length+"==="+this.chartArrayValues.length);
								}
								
								
								if(dictData["Aggregation_Data"].length >0)
								{
									//Alert.show("string data is null");
									dataInput = dictData["Aggregation_Data"] as Array;
									//Alert.show("Metrics:"+dataInput[0].Metrics);
									chartResult = ParseDataString("AggregatedArray", FormatParser.FROM_COMMA_DATE_METRIC_TO_X_AND_Y_ARRAY_COLLECTION, dictData["X_Label"] as String, dictData["Y_Label"] as String) as ArrayCollection;
								
									// flatten dataset
									if (chartResults.length == 0)
									{
										if (chartResult != null)
										{
											//Alert.show("chartREsult is not null");
											for (var m:Number = 0; m < chartResult.length; m++ )
											{
												comboDictData = new Dictionary();
											
												comboDictData[dictData["Y_Label"]] = chartResult[m][dictData["Y_Label"]];
												if ((comboDictData[dictData["Y_Label"]].indexOf("Day_Shift") < 0) && (comboDictData[dictData["Y_Label"]].indexOf("Night_Shift") < 0))
												{
													comboDictData[dictData["Y_Label"]] = comboDictData[dictData["Y_Label"]].substring(comboDictData[dictData["Y_Label"]].indexOf("_")+1);
												}
												comboDictData[a.toString()] = chartResult[m][m.toString()];
												num = new Number(chartResult[m]["total_max"] - chartResult[m][m.toString()]);
												comboDictData["max"] = new Number(num.toPrecision(3));
												
												//Alert.show("indx:"+a+"-score:"+chartResult[m][m.toString()]+"-label:"+chartResult[m][dictData["Y_Label"]]+"-max:"+chartResult[m]["total_max"]);
												//Alert.show("Max0:"+chartResult[b]["total_max"]+"<-->Value:"+chartResult[b][dictData["X_Label"]]);
												chartResults.addItem(comboDictData);	
											}
										}
										
									}
									else
									{
										for (var n:Number = 0; n < chartResults.length; n++ )
										{
											// initialize
											chartResults[n][a.toString()] = new Number(0);
											
											// find the date match
											for (var o:Number = 0; o < chartResult.length; o++ )
											{
												if (chartResult[o][dictData["Y_Label"]] == chartResults[n][dictData["Y_Label"]])
												{
													dateMatch = true;
													//Alert.show("Match!!");
													chartResults[n][a.toString()] = chartResult[o][o.toString()];
													num = new Number( chartResult[o]["total_max"] - chartResult[o][o.toString()] ) + chartResults[n]["max"];
													chartResults[n]["max"] = new Number( num.toPrecision(3));
													
													//chartResults[c]["Name"] = dictData["KPI_Name"];
													//Alert.show("Max"+a+":"+chartResult[d]["total_max"]+"<-->Value:"+chartResult[d][dictData["X_Label"]]);
											
												}
												else
												{
													dateMatch = false;
												}
											}
										}
									}
								}
								
								//Alert.show("KPI Name:"+dictData["KPI_Name"]+"-Result:"+chartResult[0][dictData["Y_Label"]]+"-Aggregatuiion data:"+dictData["Aggregation_Data"]+"-Data:"+dictData["ChartStringData"]+"-Y label:"+dictData["Y_Label"]);
								//chartResults.addItem({kpi:dictData["KPI_Name"], kpi_data:chartResult});
								//parseResults = new ArrayCollection();
							}
							//Alert.show("cart Result len:"+chartResults.length);
						}
						
						parseResults = chartResults;
					}
					catch (err:Error)
					{
						Alert.show("[ERROR] problems parsing data chart from comma date metric to x/y collection:"+err.message+":"+err.getStackTrace());
					}
					break;
				case FROM_COMMA_DATE_METRIC_TO_ARRAY_COLLECTION:
					var dateLen:Number; 
					var valueLen:Number;
					var dates:Array;
					var values:Array;
					var dateList:String;
					var valueList:String;
					var date:Array;
					var value:Array;
					var dateVal:Date;
					var metricVal:Number;
					var containsDataFlag:Boolean = false;
					
					try
					{
						if (dt == "TotalDataChart")
						{
							
							var newArrayCollection:ArrayCollection = new ArrayCollection();
							parseResults = new ArrayCollection();
							lastlineIndex = 0;
							chartResult = null;
							
							//Alert.show("In Total");
							
							// get alll the chart data
							for (var e:Number = 0; e < this.chartArrayValues.length; e++ )
							{
								dictData = this.chartArrayValues[e] as Dictionary;
								this.currentKpiName = dictData["KPI_Name"];
								this.currentCategory = dictData["Category"] as Array;
								//Alert.show("KPI NAme:"+dictData["KPI_Name"]);
								//Alert.show(currentCategory +":"+currentCategory);
								
								if ((dictData["CategoryAxisDates"] != null)&&(dictData["CategoryArray"] == null))
								{
									this.dateDataInput = dictData["CategoryAxisDates"] as Array;
									this.valueDataInput = dictData["CategoryAxisValues"] as Array;
									//Alert.show("NOT NULL!");
									//Alert.show("CA Dates:" + dateDataInput);
									//Alert.show("CA Values:" + values );
									//Alert.show("categoryAxis");
									chartResult = ParseDataString("TotalDataArray", FormatParser.FROM_COMMA_DATE_METRIC_TO_ARRAY_COLLECTION, "Dates", "Scores") as ArrayCollection;
									//Alert.show("cvhart result:" + chartResult.length);
									if (chartResult != null)
									{
										lastlineIndex = chartResult.length;
									}
									
									parseResults = chartResult;
							
								}
								
								
								if (dictData["CategoryArray"] != null)
								{
									//Alert.show("NOT NULL!!");
									dictDataArray = dictData["CategoryArray"];
									dictDataArrayCount = dictDataArray.length;
									
									var aggrDict:Dictionary;
									
									// get dates and values
									for (var f:Number = 0; f < dictDataArray.length; f++ )
									{
										aggrDict = new Dictionary();
										aggrDict = dictDataArray[f] as Dictionary;
										//Alert.show("all Dates:"+aggrDict.Dates+":"+ aggrDict.Dates.split(',').length);
										//Alert.show("all Metrics:"+aggrDict.Metrics+":"+ aggrDict.Metrics.split(',').length);
										
										if ((f ==0 ))
										{
											this.dateDataInput = new Array();
											this.valueDataInput = new Array();
										}
										
										var j:String = aggrDict.Dates;
										var k:String = aggrDict.Metrics;
										//Alert.show(aggrDict.Chart_By);
										
										
										if (j.charAt(j.length - 1) == ',')
										{
											j = j.substring(0, j.length - 1);
										}
										
										if (k.charAt(k.length - 1) == ',')
										{
											k = k.substring(0, k.length - 1);
										}
										
										var i:Array = j.split(',');
										var l:Array = k.split(',');
										//Alert.show("Dates" + j + ":"+i.length);
										//Alert.show("Metrics" + k + ":"+ l.length);
										
										// add the Dates
										for (var g:Number=0; g < i.length; g++ )
										{
											//Alert.show(i[g]);
											this.dateDataInput.push(i[g]);
											//Alert.show("date:"+dateDataInput.toString());
										}
										
										// add the Dates
										for (var h:Number=0; h < l.length; h++ )
										{
											//Alert.show(aggrDict.Metrics.split(',')[h]);
											this.valueDataInput.push(l[h]);
										}
									}
									
									//Alert.show("Dates:" + dateDataInput.toString());
									//Alert.show("Values:" + valueDataInput.toString());
									//Alert.show("DictLen:"+dictDataArray.length);
									if (i != null)
									{
										//if (i.length == l.length)
										{
											chartResult = ParseDataString("TotalDataArray", FormatParser.FROM_COMMA_DATE_METRIC_TO_ARRAY_COLLECTION, "Dates", "Scores") as ArrayCollection;
										
											if (chartResult != null)
											{
												//if (chartResult.length != 0)
												{
													//lastlineIndex = chartResult.length;
												   //Alert.show("I'm NOt Null");
													lastlineIndex++;
												}
											}
										}
									}
								}
								
								parseResults = chartResult;
							
							}
							
							if (parseResults == null)
							{
								//Alert.show("parseRes/s is null!");
							}
							//parseResults = chartResult;
							//Alert.show("beginning raw data part!"+chartResult.length);
						}
					}
					catch (err:Error)
					{
						Alert.show("[ERROR] problems parsing data chart from comma date metric to arry collection"+ err.getStackTrace());
					}
					break;
			}
			
			return parseResults;
		}
		
		public function ParseDataDictArray():void
		{
			
		}
		
		public function ParseDataStringArray():void
		{
			
		
		}
		
		public function ParseDataString(ds:String, method:int, xLabel:String = "X_Label", yLabel:String = "Y_Label"):Object
		{
			var newArrayCollection:ArrayCollection = new ArrayCollection();
			var newAC:ArrayCollection = new ArrayCollection();
			var aggregatedData:Array;
			var dict:Dictionary = new Dictionary();
			var dictData:Dictionary;
			var df:DateFormatter;
			var dataSets:Array;
			var i:Number;
			var j:Number;
			
			switch(method)
			{
				
				case FROM_COMMA_AND_PIPE_TO_X_AND_Y_ARRAY_COLLECTION:
					try
					{
						
						var commaDelimited:Array;
						var commaDelimitedValue:String;
						var pipeDelimited:Array;
						
						
						if (ds == "AggregatedArray")
						{
							// must be aggregated data array
							aggregatedData = this.dataInput as Array;
						}
						
						if (ds == "DictionaryValues")
						{
							// must be dictionary values
							
						}
						
						if (ds == "TotalDataArray")
						{
							// must be an aggregation subchart
							//Alert.show("total array business");
						}
						
						if (ds.indexOf(",") >= 0)
						{
							// must be string data
							commaDelimited = ds.split(",");
						}
						
						//this.dbgText.text = "going into parser Loop:";
						
						// get the commas
						for (i = 0; i < commaDelimited.length; i++ )
						{
							// get the actual value
							commaDelimitedValue = commaDelimited[i];
						
							//this.dbgText.text = "got comma delimited:";
							
							// now for the pipes
							pipeDelimited = commaDelimitedValue.split("|");
							
							dict = new Dictionary();
							df = new DateFormatter();
							df.formatString = "MM/DD";
							
							//this.dbgText.text += "x="+pipeDelimited[0]+"&y="+pipeDelimited[1]+":";
							
							
							dict[xLabel] = new Number(pipeDelimited[1]); //x
							//dict[yLabel] = new String(pipeDelimited[0]); //y
							dict[yLabel] = new String(df.format(pipeDelimited[0])); //y
							//dict["Gold"] = new Number(50); //x
							//dict["Country"] = new String("test1"); //y
							
							
							
							// add each array
							newArrayCollection.addItem(dict);
							//newArrayCollection = new ArrayCollection([{Country:"test1", Gold:50},{Country:"test2", Gold:30}]);
							
						}
						
						// format dates
						
						// sort by date
						
						
						parseResults = newArrayCollection;
						lastParseFormat = FROM_COMMA_AND_PIPE_TO_X_AND_Y_ARRAY_COLLECTION;
					}
					catch (err:Error)
					{
						this.dbgText.text = "[ERROR] problems parsing string data FROM_COMMA_AND_PIPE_TO_ARRAY_COLLECTION:" + err.message;
						Alert.show("[ERROR] problems parsing string data FROM_COMMA_AND_PIPE_TO_ARRAY_COLLECTION:"+ err.message);
					}
					
					break;
				case FROM_COMMA_DATE_METRIC_TO_ARRAY_COLLECTION:
					
					
					var dateLen:Number; 
					var valueLen:Number;
					var dates:Array;
					var values:Array;
					var dateList:String;
					var valueList:String;
					var date:Array;
					var value:Array;
					var dateVal:Date;
					var metricVal:Number;
					var containsDataFlag:Boolean = false;
					var startingIndx:Number = 0;
					
					try 
					{
						
						if((ds == "TotalDataArray"))
						{
							//Alert.show(":"+this.currentKpiName);
							newArrayCollection = new ArrayCollection();
							dates = this.dateDataInput as Array;
							values = this.valueDataInput as Array;
							
							
							if (this.lastlineIndex == 0)
							{
								parseResults = new ArrayCollection();
							}
							else
							{
								startingIndx = lastlineIndex;
							}
							
							//Alert.show("date:" + dates + ":" + dates.length);
							//Alert.show("value:" + values + ":" + values.length);
							//Alert.show("dateArrays:"+dates.length);
							if (dates != null) // if we have dates
							{
								dateLen = dates.length;
								valueLen = values.length;
								
								// regular chart
								for (var b:Number = 0; b < dates.length; b++ )
								{
									dateList = dates[b];
									valueList = values[b];
									
									if (dates.length == 1)
									{
										//Alert.show("dateList:"+dateList+" value:"+valueList);
										//Alert.show("valueList:"+valueList);
									}
									if (dateList.charAt(0) == ',')
									{
										dateList = dateList.substr(1);
									}
									
									if (currentCategory == null)
									{
										this.currentCategory = new Array();
									}
									
									if ((this.currentCategory[b] == null))
									{
										this.currentCategory[b] = "Operator";
										//Alert.show("dangit");
									}
									
									if ((this.currentCategory[b] == ""))
									{
										this.currentCategory[b] = "Empty";
										//Alert.show("dangit");
									}
									
									if (dateList == null||valueList==null)
									{
										dateList = "";
										valueList = "";
									}
									
									// might be only one point in the set
									if ((dateList.indexOf(",")>=0) && (valueList.indexOf(",")>=0))
									//if (dateList!=null && (valueList!=null))
									{
										//if (dateList.indexOf(",") >= 0)
										{
											// haldnle single values
											if (dateList.indexOf(",") >= 0)
											{
												date = dateList.split(",");
												value = valueList.split(",");
											}
											else
											{
												date = new Array();
												//value = new Array();
												date[0] = dateList;
												value = valueList.split(",");
											}
											
											//Alert.show("date:"+date+":Len="+date.length);
											//Alert.show("value:" + value + ":Len=" + value.length+ ":Op-"+ this.currentCategory[b]+":kpi"+this.currentKpiName);
											
											if (date.length > 1)
											{
												//Alert.show("date:"+value+":Len="+date.length);
												//Alert.show("adding data-op:"+this.currentCategory[b]);
												for(var a:Number = 0; a <= date.length; a++)
												{
													dateVal = new Date();
													try
													{
														if ((date[a].length > 0))
														{
															dateVal.setTime(Date.parse(date[a]));
															
															metricVal = new Number(value[a]);
															//Alert.show("metricVal-"+metricVal.toString()+":val="+value);
															if (metricVal.toString() == Number.NaN.toString())
															{
																//Alert.show("not a number");
															}
															else
															{
																newArrayCollection.addItem({date:dateVal, score:metricVal.toPrecision(3), kpi:this.currentKpiName});
																//Alert.show("b=" + b + "a=" + a + "N=" + dateVal + "&" + metricVal + " with the size:" + newArrayCollection.length);
																containsDataFlag = true;
															}
															
															//Alert.show("N=" + dateVal.toString() + ":" + metricVal.toString());
														
															
				
														}
													}
													catch (err:Error)
													{
														//Alert.show("bad date?"+err.message);
													}
												}
												
												//Alert.show("lastLineIndex:"+lastlineIndex);
												if (startingIndx == 0)
												{	
													if ((date.length > 1)&& this.currentCategory[b]!="Empty")
													{
														//Alert.show("al-cnt1:"+newArrayCollection.length+":val"+valueLen+":Op"+this.currentCategory[b]+":"+this.currentKpiName);
														parseResults.addItem( { axis:b, coll:newArrayCollection, kpi:this.currentKpiName, cat:this.currentCategory[b] } );
													}
													
													//startingIndx++;
												}
												else
												{
													//Alert.show("al-cnt2:"+newArrayCollection.length+":val"+valueLen+":Op"+this.currentCategory[b]);
													if ((date.length > 1)&& this.currentCategory[b]!="Empty")
													{
														//Alert.show(currentKpiName);
														parseResults.addItem( { axis:lastlineIndex, coll:newArrayCollection, kpi:this.currentKpiName, cat:this.currentCategory[b] } );
													}
												}
												
												newArrayCollection = new ArrayCollection();
											}
											else
											{
												//Alert.show("using dateList:"+dateList);
												dateVal = new Date();
												if (b == 0)
												{
													newArrayCollection = new ArrayCollection();
												}
												
												try
												{
													if ((dateList.length > 0))
													{
														dateVal.setTime(Date.parse(dateList));
														
														metricVal = new Number(valueList);
													
														//Alert.show("date="+dateVal.toString()+":metric="+metricVal.toString()+":"+Number.NaN.toString());
														if (metricVal.toString() == Number.NaN.toString())
														{
															//Alert.show("not a number:"+(value is Array));
															if ((value is Array)&&(dates.length ==1)&&(dateList.indexOf(",")<0)&&(date.length == 1))
															{
																metricVal = new Number(value[0]);
																//Alert.show("date="+dateVal.toString()+":metric="+metricVal.toString());
																if(metricVal.toString() != Number.NaN.toString())
																{
																	newArrayCollection.addItem({date:dateVal, score:metricVal.toPrecision(3), kpi:this.currentKpiName,cat:this.currentCategory[b]});
																	//Alert.show("b=" + b + "a=" + a + "N=" + dateVal + "&" + metricVal + " with the size:" + newArrayCollection.length);
																	containsDataFlag = true;
																}
															}
														}
														else
														{
															newArrayCollection.addItem({date:dateVal, score:metricVal.toPrecision(3), kpi:this.currentKpiName,cat:this.currentCategory[b]});
															//Alert.show("b=" + b + "a=" + a + "N=" + dateVal + "&" + metricVal + " with the size:" + newArrayCollection.length);
															containsDataFlag = true;
														}
														
														//Alert.show("N=" + dateVal.toString() + ":" + metricVal.toString());
														//Alert.show("coll-len:" + parseResults.length);
													}
													else
													{
														
														if (this.chartArrayValues == null)
														{
															newArrayCollection.addItem( { date:new Date(), score:0, kpi:"" } );
															parseResults.addItem( { axis:b, coll:newArrayCollection, kpi:this.currentKpiName, cat:this.currentCategory[b] } );
															newArrayCollection = new ArrayCollection();
															//Alert.show("hey!"+b+"chartArray="+this.chartArrayValues.length);
														}
														
													}
												}
												catch (err:Error)
												{
													Alert.show("bad date?"+err.message);
												}
												
												//Alert.show("lastLineIndex:"+lastlineIndex+ ":"+ newArrayCollection.length+"parseResults len:"+parseResults.length);
												if ((b == dates.length-1)&& this.currentCategory[b]!="Empty")
												{
													parseResults.addItem( { axis:lastlineIndex, coll:newArrayCollection, kpi:this.currentKpiName, cat:this.currentCategory[b] } );
												}
												//newArrayCollection = new ArrayCollection();
											}
										}
										
									}
									else
									{
										// try categoryArray
										//Alert.show("test:"+dateList+"*");
										/*
										if ((dateList.length > 0)&&dictDataArrayCount==1)
										{
											//Alert.show("test:"+dateList+"*");
											dateVal = new Date();
											if (b == 0)
											{
												newArrayCollection = new ArrayCollection();
											}
											
											dateVal.setTime(Date.parse(dateList));
											metricVal = new Number(valueList);
										
											//Alert.show("date="+dateVal.toString()+":metric="+metricVal.toString());
											if (metricVal.toString() == Number.NaN.toString())
											{
												//Alert.show("not a number");
											}
											else
											{
												newArrayCollection.addItem({date:dateVal, score:metricVal.toPrecision(3), kpi:this.currentKpiName,cat:this.currentCategory[b]});
												//Alert.show("b=" + b + "a=" + a + "N=" + dateVal + "&" + metricVal + " with the size:" + newArrayCollection.length);
												containsDataFlag = true;
											}
											
											if ( this.currentCategory[b]!="Empty")
											{
												parseResults.addItem( { axis:lastlineIndex, coll:newArrayCollection, kpi:this.currentKpiName, cat:this.currentCategory[b] } );
											}
										}
										*/
										//Alert.show("basic new Array:"+newArrayCollection.length);
										//parseResults = null;
									}
								}
							}
							
							if (!containsDataFlag)
							{
								//parseResults = null;
							}
							//parseResults = newArrayCollection;
							//lastParseFormat = FROM_COMMA_DATE_METRIC_TO_ARRAY_COLLECTION;
							//Alert.show("length of cat axis stuffs:" + newArrayCollection.length);
							//Alert.show("length of cat axis stuffs as parseResults:"+parseResults.length);
							//Alert.show("pr is:"+parseResults);
							//Alert.show("basic new Array:"+newArrayCollection.length);
							
						}
					}
					catch (Exc:Error)
					{
						//this.dbgText.text = "[ERROR] problems parsing string data FROM_COMMA_DATE_METRIC_TO_ARRAY_COLLECTION:" + Exc.message;
						Alert.show("TDA-issues!"+this.dbgText.text + ":"+ Exc.getStackTrace());
						parseResults = new ArrayCollection();
					}
					break;
				case FROM_COMMA_DATE_METRIC_TO_X_AND_Y_ARRAY_COLLECTION:
					try
					{
						var commaDelimitedDates:Array;
						var commaDelimitedMetrics:Array;
						var calculationFrequencies:Array;
						var dataSetToAverage:Array;
						var commaDelimitedDateValue:String;
						var commaDelimitedMetricValue:String;
						var calculationValue:Number;
						
						this.dbgText.text = ">in comma date metric";
						//Alert.show("charting Stuffs:"+ds);
						if (ds == "AggregatedArray")
						{
							//Alert.show("Aggregate Array:"+this.dataInput.length);
							// must be aggregated data array
							aggregatedData = this.dataInput as Array;
							commaDelimitedDates = new Array(); // TODO: change to labels
							commaDelimitedMetrics = new Array();
							calculationFrequencies = new Array();
							
							//this.dbgText.text = "in aggregated array";
							
							// loop through dict and calculate for current ChartBy label
							for (i = 0; i < aggregatedData.length; i++ )
							{
								dictData = aggregatedData[i];
								var tmpAvgVal:Number = 0;
								var tmpNumVal:Number = 0;
								var dctr:Number = 0;
								
								// aggregating by month
								if (dictData["Month"] != null)
								{
									//Alert.show("month apparently");
									commaDelimitedDates.push(dictData.Month);
									//Alert.show("mon:"+dictData.Month+"\ndataSet:" + dictData.Metrics+"\ndateSet:" + dictData.Dates+"\n");
									// remove leading comma
									if (dictData.Metrics.charAt(0) == ',')
									{
										dictData.Metrics = dictData.Metrics.substring(1);
									}
									dataSetToAverage = dictData.Metrics.split(",");
									
									
									this.dbgText.text += "dataSet :\n" + dictData.Metrics;
									
									
									for (j = 0; j < dataSetToAverage.length; j++)
									{
										tmpNumVal = dataSetToAverage[j];
										
										if (tmpNumVal >= 0)
										{
											// add em up
											tmpAvgVal += tmpNumVal;
										}
										else
										{
											dctr--;
										}
									}
									
									//this.dbgText.text += "dataSet av tot:"+tmpAvgVal;
									//Alert.show("av-val :\n" + tmpAvgVal);
									//Alert.show("av-num :\n" + (j + dctr));
									
									// divide by total
									tmpAvgVal = tmpAvgVal / (j + dctr);
									
									if (tmpAvgVal.toString() == Number.NaN.toString())
									{
										tmpAvgVal = 0;
									}
									
									commaDelimitedMetrics.push(tmpAvgVal.toPrecision(3));
								}
								else
								{
									//Alert.show("Not month for sure");
									// aggregating by category
									calculationFrequencies.push(dictData.Frequency);
									
									commaDelimitedDates.push(dictData.Chart_By);
									
									// remove leading comma
									if (dictData.Metrics.charAt(0) == ',')
									{
										dictData.Metrics = dictData.Metrics.substring(1);
									}
									
									dataSetToAverage = dictData.Metrics.split(",");
									//Alert.show("dataSet :\n" + dictData.Metrics+ "len:"+dataSetToAverage.length);
									
									this.dbgText.text += "*non mon data:\n"+dictData.Metrics;
									
									for (j = 0; j < dataSetToAverage.length; j++)
									{
										tmpNumVal = dataSetToAverage[j];
										
										if (tmpNumVal >= 0)
										{
											// add em up
											tmpAvgVal += tmpNumVal;
										}
										else
										{
											dctr--;
										}
									}
									
									//Alert.show("av-val :\n" + tmpAvgVal);
									//Alert.show("av-num :\n" + (j + dctr));
									this.dbgText.text += "dataSet av tot:"+tmpAvgVal;
									
									// divide by total
									tmpAvgVal = tmpAvgVal / (j+dctr);
									
									if (tmpAvgVal.toString() == Number.NaN.toString())
									{
										tmpAvgVal = 0;
									}
									
									commaDelimitedMetrics.push(tmpAvgVal.toPrecision(3));
								
								}
							}
						}
						
						if(ds == "TotalDataArray")
						{
							dataSets = this.dateDataInput as Array;
							//Alert.show("number of date sets:"+dataSets.length);
						}
						else
						{
							//Alert.show("in the house:"+ds);
							if (ds.indexOf(",") >= 0)
							{
								this.dbgText.text = "must be a data string";
								//Alert.show(this.dbgText.text);
								
								// must be string data
								dataSets = ds.split("}{");
							
								commaDelimitedDates= dataSets[0].split(",");
								commaDelimitedMetrics = dataSets[1].split(",");
								//Alert.show("right here");
							}
							else
							{
								//Alert.show("no comma:"+ds.length);
								if ((ds.length > 0)&&(ds!="AggregatedArray")&&(ds!="DictionaryValues"))
								{
									dataSets = ds.split("}{");
									commaDelimitedDates = new Array();
									commaDelimitedDates.push(dataSets[0]);
									//Alert.show(dataSets[0] + ":" + dataSets[1]);
									commaDelimitedMetrics = new Array();
									commaDelimitedMetrics.push(dataSets[1]);
									//Alert.show(dataSets[0] +":"+dataSets[1]);
								}
							}
						}
						//Alert.show("dates:"+commaDelimitedDates);
						//Alert.show("metrics:"+commaDelimitedMetrics);
						//this.dbgText.text = "going into parser Loop:";
						//Alert.show("KPI "+this.kpiMaxValues[0]);
						// get the commas
						for (i = 0; i < commaDelimitedDates.length; i++ )
						{
							// get the actual value
							commaDelimitedDateValue = commaDelimitedDates[i];
							commaDelimitedMetricValue = commaDelimitedMetrics[i];
							
							//this.dbgText.text = "got comma delimited:";
							
							dict = new Dictionary();
							df = new DateFormatter();
							df.formatString = "MM/DD/YYYY";
			
							//this.dbgText.text += "x="+commaDelimitedMetricValue+"&y="+commaDelimitedDateValue+":";
							
							//dict[xLabel] = new Number(commaDelimitedMetricValue); //x
							
							
							if ((ds != "AggregatedArray") && (ds != "DictionaryValues"))
							{
								dict[xLabel] = new Number(commaDelimitedMetricValue); //x
								dict[yLabel] = new String(df.format(commaDelimitedDateValue)); //y
								if ((dict[yLabel] != "Day_Shift") && (dict[yLabel] != "Night_Shift"))
								{
									dict[yLabel] = dict[yLabel].substring(dict[yLabel].indexOf("_") + 1); 
								}
								var a_max:Number = new Number(this.kpiMaxValues[0] - dict[xLabel]); 
								dict["max"] = a_max.toPrecision(3);
								dict["total_max"] = new Number(this.kpiMaxValues[0]);
								//Alert.show("KPI Max="+dict["max"]+":"+this.kpiMaxValues[0]+":"+dict[xLabel]+":"+);
								//Alert.show("KPI:"+this.kpiMaxValues[0]+"yLabel:"+dict[yLabel]);
								sortField = yLabel;
							}
							else
							{
								//Alert.show("yLabel:"+yLabel);
								dict[i.toString()] = new Number(commaDelimitedMetricValue); //x
								dict[yLabel] = new String(commaDelimitedDateValue); //y
								if ((dict[yLabel] != "Day_Shift") && (dict[yLabel] != "Night_Shift"))
								{
									dict[yLabel] = dict[yLabel].substring(dict[yLabel].indexOf("_") + 1); 
								}
								
								a_max =  new Number(this.kpiMaxValues[0] - dict[i.toString()] );
								dict["max"] = a_max.toPrecision(3);
								dict["total_max"] = new Number(this.kpiMaxValues[0]);
								dict["kpi"] = this.currentKpiName;
								//Alert.show(this.currentKpiName);
							}
							
							if (ds == "AggregatedArray")
							{
								calculationValue = calculationFrequencies[i];
								
								var testNum:Number = new Number(calculationValue);
								
								if (testNum.toString() != Number.NaN.toString())
								{
									dict["Shift Count"] = new Number(calculationValue);
								}
							}
							
							//dict["Gold"] = new Number(50); //x
							//dict["Country"] = new String("test1"); //y
							
							// add each array
							newArrayCollection.addItem(dict);
							
						}
						
						
						if ((ds != "AggregatedArray") && (ds != "DictionaryValues"))
						{
							newAC = this.qsort(newArrayCollection);
						}
						else
						{
							newAC = newArrayCollection;
						}
						
						//Alert.show("ac len:"+newAC.length);
						
						parseResults = newAC;
						lastParseFormat = FROM_COMMA_DATE_METRIC_TO_X_AND_Y_ARRAY_COLLECTION;
					}
					catch (err:Error)
					{
						this.dbgText.text = "[ERROR] problems parsing string data FROM_COMMA_DATE_METRIC_TO_X_AND_Y_ARRAY_COLLECTION:" + err.message;
						//Alert.show("issues!");
						if (parseResults == null)
						{
							parseResults = new ArrayCollection();
						}
					}
					
					break;
			}
			
			return parseResults;
		}
		
		public function ParseCurrentData(method:int, xLabel:String = "X_Label", yLabel:String = "Y_Label"):Object
		{
			var result:Object; 
			var acol:ArrayCollection = this.parseResults as ArrayCollection;
			var sArray:Array = new Array();
						
			switch(method)
			{
				case FROM_CURRENT_DATA_TO_BARSERIES_ARRAY:
					try
					{
						//this.dbgText.text += "in correct methid";
						//var acol:ArrayCollection = this.parseResults as ArrayCollection;
						var bSeries:BarSeries = new BarSeries();
						//var sArray:Array = new Array();
						
						if (this.lastParseFormat == FROM_COMMA_AND_PIPE_TO_X_AND_Y_ARRAY_COLLECTION)
						{
							
							//for (var i:Number = 0; i < acol.length; i++ )
							{
								bSeries.xField = xLabel;
								bSeries.yField = yLabel;
								bSeries.displayName = "Score";
								
								//bSeries.xField = "Gold";
								//bSeries.yField = "Country";
								//bSeries.displayName = "Gold";
								
								sArray.push(bSeries);
							}
						}
						
						if (this.lastParseFormat == FROM_COMMA_DATE_METRIC_TO_X_AND_Y_ARRAY_COLLECTION)
						{
							bSeries.xField = xLabel;
							bSeries.yField = yLabel;
							bSeries.displayName = "Score";
								
							sArray.push(bSeries);
							
						}
						
						result = sArray;
						//lastParseFormat = FROM_CURRENT_DATA_TO_BARSERIES_ARRAY;
					}
					catch (err:Error)
					{
						this.dbgText.text = "[ERROR] problems parsing string data FROM_CURRENT_DATA_TO_BARSERIES_ARRAY:"+err.message;
					}
					
					break;
			
				case FROM_CURRENT_DATA_TO_LINESERIES_ARRAY:
					try
					{
						
						var lSeries:LineSeries = new LineSeries();
						
						if (this.lastParseFormat == FROM_COMMA_AND_PIPE_TO_X_AND_Y_ARRAY_COLLECTION)
						{
							
							//for (var i:Number = 0; i < acol.length; i++ )
							{
								lSeries.xField = xLabel;
								lSeries.yField = yLabel;
								lSeries.displayName = "Score";
								
								sArray.push(lSeries);
							}
						}
						
						if (this.lastParseFormat == FROM_COMMA_DATE_METRIC_TO_X_AND_Y_ARRAY_COLLECTION)
						{
							lSeries.xField = xLabel;
							lSeries.yField = yLabel;
							lSeries.displayName = "Score";
							//Alert.show("plotted data");
							sArray.push(lSeries);
							
						}
						
						result = sArray;
						
					}
					catch (err:Error)
					{
						this.dbgText.text = "[ERROR] problems parsing string data FROM_CURRENT_DATA_TO_LINESERIES_ARRAY:"+err.message;
					}
					
					break;
				case FROM_CURRENT_DATA_TO_AREASERIES_ARRAY:
					try
					{
						
						var aSeries:AreaSeries = new AreaSeries();
						
						if (this.lastParseFormat == FROM_COMMA_AND_PIPE_TO_X_AND_Y_ARRAY_COLLECTION)
						{
							
							//for (var i:Number = 0; i < acol.length; i++ )
							{
								aSeries.xField = xLabel;
								aSeries.yField = yLabel;
								aSeries.displayName = "Score";
								
								sArray.push(aSeries);
							}
						}
						
						if (this.lastParseFormat == FROM_COMMA_DATE_METRIC_TO_X_AND_Y_ARRAY_COLLECTION)
						{
							aSeries.xField = xLabel;
							aSeries.yField = yLabel;
							aSeries.displayName = "Score";
							//Alert.show("plotted data");
							sArray.push(aSeries);
							
						}
						
						result = sArray;
						
					}
					catch (err:Error)
					{
						this.dbgText.text = "[ERROR] problems parsing string data FROM_CURRENT_DATA_TO_AREASERIES_ARRAY:"+err.message;
					}
					
					break;
				case FROM_CURRENT_DATA_TO_COLUMNSERIES_ARRAY:
					try
					{
						
						var cSeries:ColumnSeries = new ColumnSeries();
						
						if (this.lastParseFormat == FROM_COMMA_AND_PIPE_TO_X_AND_Y_ARRAY_COLLECTION)
						{
							
							//for (var i:Number = 0; i < acol.length; i++ )
							{
								cSeries.xField = xLabel;
								cSeries.yField = yLabel;
								cSeries.displayName = "Score";
								
								sArray.push(cSeries);
							}
						}
						
						if (this.lastParseFormat == FROM_COMMA_DATE_METRIC_TO_X_AND_Y_ARRAY_COLLECTION)
						{
							cSeries.xField = xLabel;
							cSeries.yField = yLabel;
							cSeries.displayName = "Score";
							//Alert.show("plotted data");
							sArray.push(cSeries);
							
						}
						
						result = sArray;
						
					}
					catch (err:Error)
					{
						this.dbgText.text = "[ERROR] problems parsing string data FROM_CURRENT_DATA_TO_LINESERIES_ARRAY:"+err.message;
					}
					
					break;
					
				case FROM_CURRENT_DATA_TO_DICTIONARY_ARRAY:
					try
					{
						if (this.lastParseFormat == FROM_CURRENT_DATA_TO_DICTIONARY_ARRAY)
						{
							result = this.parseResults as Array;
						}
					}
					catch (err:Error)
					{
						this.dbgText.text = "[ERROR] problems parsing string data FROM_CURRENT_DATA_TO_DICTIONARY_ARRAY:"+err.message;
					}
					
					break;
					
				case FROM_CURRENT_DATA_TO_ARRAY_COLLECTION:
					try
					{
						if (this.lastParseFormat == FROM_COMMA_AND_PIPE_TO_X_AND_Y_ARRAY_COLLECTION)
						{
							result = this.parseResults as ArrayCollection;
						}
						
						if (this.lastParseFormat == FROM_COMMA_DATE_METRIC_TO_X_AND_Y_ARRAY_COLLECTION)
						{
							result = this.parseResults as ArrayCollection;
						}
					}
					catch (err:Error)
					{
						this.dbgText.text = "[ERROR] problems parsing string data FROM_CURRENT_DATA_TO_ARRAY_COLLECTION:"+err.message;
					}
					
					break;
			}
			
			return result;
		}
		
		private function ascendingCompareDates (a:Dictionary, b:Dictionary):Number
		{
			//Alert.show("test:"+sortField);
			var dict:Dictionary = a as Dictionary;
			var dateA:Number = Date.parse(a[this.sortField]);
			var dateB:Number = Date.parse(b[this.sortField]);
			
			
			return dateA - dateB;
		}
		
		  
		private function qsort(a:ArrayCollection):ArrayCollection
		{
			if (a.length <= 1)
			{
				return a;
			}
	   
			var dict:Dictionary = a[0];
			var p:Dictionary = a[0];
			var gr:ArrayCollection = new ArrayCollection();
			var ls:ArrayCollection = new ArrayCollection();
			
			try
			{
				//Alert.show(a[0]);
			
			  for (var i:Number = 1; i < a.length; i++)
			  {
				dict = a[i];
				//Alert.show(Date.parse(dict[sortField])+":"+p)
				if (Date.parse(dict[sortField]) < Date.parse(p[sortField]))
				{
					//ls.push(a[i]);
					ls.addItem(a[i]);
				}
				else 
				{
					//gr.push(a[i]);
					gr.addItem(a[i]);
				}
			  }
		  
			  ls = qsort(ls);
			  gr = qsort(gr);
			  //ls.push(p)
			  ls.addItem(p);
			  ls.addAll(gr.list);
			}
			catch (err:Error)
			{
				Alert.show("ERROR-" + err.message );
			}
			
		  return ls; 
			//return gr;
      }
	}
}