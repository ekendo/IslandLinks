package com.airliquide.alps.chart
{
	// AS3 SDK
	import flash.display.*;
	import flash.utils.*;
	
	/**
	 * ...
	 * @author earl.lawrence
	 */
	public class ALChart
	{
		// Chart Renderer Frameworks/Engines
		public static  var XML_SWF:int = 0;
		public static  var OPEN_FLASH:int = 1;
		public static  var AM:int = 2;
		public static  var YAHOO:int = 3;
		public static  var FLEX:int = 4;
		
		// Chart Data Types
		public static  var CSV_FILE:int = 0;
		public static  var HTML_VARIABLES:int = 1;
		public static  var XML_FILE:int = 2;
		public static  var JSON_FORMAT:int = 3;
		public static  var CSV_FORMAT:int = 4;
		public static  var XML_FORMAT:int = 5;
		public static  var DBF_FILE:int = 6;
		public static  var DATABASE:int = 7;
		
		// Chart Basic Display Formats
		public static  var BAR:int = 0;
		public static  var PIE:int = 1 ;
		public static  var AREA:int = 2;
		public static  var LINE:int = 3;
		public static  var AXIS:int = 4;
		public static  var BUBBLE:int = 5;
		public static  var COLUMN:int = 6;
		public static  var CANDLESTICK:int = 7;
		public static  var GANT:int = 8;
		public static  var DONUT:int = 9;
		public static  var COLUMN_AREA:int = 10; 
		
		// Filter Types
		public static  var Chart_By:int = 0;
		public static  var Filter_By:int = 1;
		public static  var Label_X_By:int = 2;
		public static  var Label_Y_By:int = 3;
		public static  var Label_Slice_By:int = 4;
		public static  var Start_X_From:int = 5;
		public static  var End_X_To:int = 6;
		public static  var Start_Y_From:int = 7;
		public static  var End_Y_To:int = 8;
		public static  var Axis_Values_For_X:int = 9;
		public static  var Axis_Values_For_Y:int = 10;
		public static  var Calculation_By:int = 11;
		
		// Handled Calculation Type values
		public static  var By_Calculation_Average:int = 0;
		
		// Types
		private var _renderer:int;
		private var _dataFormat:int;
		private var _displayFormat:int;
		private var _filterType:int;
		private var _calculationType:int;
		
		// Flags
		private var _includeTabularValues:Boolean;
		private var _chartOnly:Boolean;
		private var _tabularOnly:Boolean;
		private var _includeFilters:Boolean;
		private var _reportFormat:Boolean;
		private var _pipeLineReport:Boolean;
		private var _inspectionReport:Boolean;
		private var _kpiReport:Boolean;
		private var _pipedValues:Boolean;
		private var _chartArrayValues:Boolean;
		private var _dictArrayValues:Boolean;
		private var _dictValues:Boolean;
		private var _3dView:Boolean;
		
		// Data Values
		private var aggreData:Array;
		private var categoryDates:Array;
		private var categoryValues:Array;
		private var categoryArray:Array;
		private var caKPIMaxData:Array;
		private var caKPI_Data:Array;
		private var kpiCategory:Array;
		
		private var calcData:Dictionary;
		private var kpiData:Dictionary;
		
		private var csData:String; // chart string data
		
		// Label Values
		private var xLabel:String;
		private var yLabel:String;
		private var kpiName:String;
		
		
		public function ALChart() 
		{
			_pipedValues = false;
			_dictArrayValues = false;
			_dictValues = false;
			_chartArrayValues = false;
			_3dView = true;
		}
		
		public function get ThreeDView():Boolean
		{
			return _3dView;
		}
		
		public function get PipedValues():Boolean
		{
			return _pipedValues;
		}
		
		public function get DictValues():Boolean
		{
			return _dictValues;
		}
		
		public function get DictArrayValues():Boolean
		{
			return _dictArrayValues;
		}
		
		public function get ChartArrayValues():Boolean
		{
			return this._chartArrayValues;
		}
		
		public function set Renderer(r:int):void 
		{
			this._renderer = r;
		}
		
		public function get Renderer():int
		{
			return _renderer;
		}
		
		public function set DataFormat(d:int):void 
		{
			this._dataFormat = d;
		}
		
		public function get DataFormat():int
		{
			return _dataFormat;
		}
		
		public function set DisplayFormat(d:int):void 
		{
			this._displayFormat = d;
		}
		
		public function get DisplayFormat():int
		{
			return _displayFormat;
		}
		
		public function set ThreeDView(v:Boolean):void
		{
			this._3dView = v;
		}
		
		public function set FilterType(f:int):void 
		{
			this._filterType = f;
		}
		
		public function get FilterType():int
		{
			return _filterType;
		}
	
		public function set CalculationType(i:int):void 
		{
			this._calculationType = i;
		}
		
		public function get CalculationType():int
		{
			return _calculationType;
		}

		/* Sets */
		public function SetCategory(cat:String):void
		{
			if (kpiCategory == null)
			{
				kpiCategory = new Array();
			}
			//this.kpiCategory= cat;
			kpiCategory.push(cat);
		}
		
		public function SetData(data:String):void
		{
			if (data.indexOf("|")>=0)
			{
				this._pipedValues = true;
			}
			else
			{
				this._pipedValues = false;
			}
			
			this.csData = data;
		
			this._dictArrayValues = false;
			this._dictValues = false;
		}
		
		public function SetDateData(data:String):void
		{
			this._dictArrayValues = false;
			this._dictValues = false;
			this._pipedValues = false;
			this.csData = data+ "}";
		}
		
		public function SetMetricData(data:String):void
		{
			this._dictArrayValues = false;
			this._dictValues = false;
			this._pipedValues = false;
			this.csData += "{"+ data;
		}
		
		public function SetMetricKPIMax(max:Number):void
		{
			if (caKPIMaxData == null)
			{
				caKPIMaxData = new Array();
			}
			
			caKPIMaxData.push(max);
		}
		
		public function SetAverageData(data:String):void
		{
			this.csData += "}{"+ data;
		}
		
		public function SetCalculationData(data:Dictionary):void
		{
			this.calcData = data;
			
			// check for piped or not
			if (data["Data"].indexOf("|")>=0)
			{
				this._pipedValues = true;
			}
			else
			{
				this._pipedValues = false;
			}
			
			this._dictArrayValues = false;
			this._dictValues = true;
		}
		
		public function SetAggregationData(data:Array):void
		{
			this.aggreData = data;
			this._dictArrayValues = true;
			this._dictValues = false;
			
			if (data != null)
			{
				// check for piped or not
				var dict:Dictionary = data[0];
			
				if (dict != null)
				{
					if (dict.Data.indexOf("|")>=0)
					{
						this._pipedValues = true;
					}
				}
			}
		}
		
		public function SetCategoryAxisDates(dates:String):void
		{
			if (dates != null)
			{
				if (this.categoryDates == null)
				{
					this.categoryDates = new Array();
				}
				
				this.categoryDates.push(dates);
			}
		}
		
		public function SetCategoryAxisValues(values:String):void
		{
			if (values != null)
			{
				if (this.categoryValues == null)
				{
					this.categoryValues = new Array();
				}
				
				this.categoryValues.push(values);
			}
		}
		
		public function SetCategoryAxisArray(ary:Array):void
		{
			this.categoryArray = ary;
		}
		
		public function SetCurrentKPI_Data(name:String):void
		{
			// make sure the array is not null
			if (this.caKPI_Data == null)
			{
				this.caKPI_Data = new Array();
			}
			
			// make sure the dictionary is initialized
			if (this.kpiData == null)
			{
				this.kpiData = new Dictionary();
			}
			
			// copy over what we have here at the moment
			this.kpiData["Aggregation_Data"] = this.aggreData;
			this.kpiData["CategoryAxisDates"] = this.categoryDates;
			this.kpiData["CategoryAxisValues"] = this.categoryValues;
			this.kpiData["CategoryArray"] = this.categoryArray;
			this.kpiData["KPI_MaxData"] = this.caKPIMaxData;
			this.kpiData["CalculationData"] = this.calcData;
			this.kpiData["ChartStringData"] = this.csData;
			this.kpiData["X_Label"] = this.xLabel;
			this.kpiData["Y_Label"] = this.yLabel;
			this.kpiData["KPI_Name"] = name;
			this.kpiData["Category"] = this.kpiCategory;
			
			this.caKPI_Data.push(this.kpiData);
			this.kpiData = new Dictionary();
			this._chartArrayValues = true;
		}
		
		public function SetKpiIndex(indx:Number):void
		{
			if (this.caKPI_Data != null)
			{
				if (this.caKPI_Data[indx] == null)
				{
					// get the last record
					this.kpiData = this.caKPI_Data.pop();
				}
				else
				{
					this.kpiData = this.caKPI_Data[indx];
				}
				
				this.aggreData = this.caKPI_Data["Aggregation_Data"];
				this.categoryDates = this.caKPI_Data["CategoryAxisDates"];
				this.categoryValues = this.caKPI_Data["CategoryAxisValues"];
				this.categoryArray = this.caKPI_Data["CategoryArray"];
				this.caKPIMaxData = this.caKPI_Data["KPI_MaxData"];
				this.calcData = this.caKPI_Data["CalculationData"];
				this.csData = this.caKPI_Data["ChartStringData"];
				this.xLabel = this.caKPI_Data["X_Label"];
				this.yLabel = this.caKPI_Data["Y_Label"];
				this.kpiName = this.caKPI_Data["KPI_Name"];
				this.kpiCategory = this.caKPI_Data["Category"];
				
			}	
		}
		
		public function SetXLabel(l:String):void
		{
			this.xLabel = l;
		}
		public function SetYLabel(l:String):void
		
		{
			this.yLabel = l;
		}
		
		public function SetKPIName(n:String):void
		{
			this.kpiName = n;
		}
		
		/* Gets */
		public function GetKPICategory():Array
		{
			return this.kpiCategory;
		}
		
		public function GetKPIName():String
		{
			return this.kpiName;
		}
		
		public function GetKPISetValues():Array
		{
			return this.caKPI_Data;
		}
		
		public function GetKPISetTotal():Number
		{
			var kpiChartTotal:Number = 0;
			
			if (this.caKPI_Data != null)
			{
				kpiChartTotal = this.caKPI_Data.length;
			}
			
			return kpiChartTotal;
		}
		
		public function GetKPIMaxData():Array
		{
			return this.caKPIMaxData;
		}
			
		public function GetKPIData():Dictionary
		{
			return this.kpiData;
		}
		
		public function GetCategoryAxisDates():Array
		{
			return this.categoryDates;
		}
		
		public function GetCategoryAxisValues():Array
		{
			return this.categoryValues;
		}
		
		public function GetData():String
		{
			return this.csData;
		}
		
		public function GetCalculationData():Dictionary
		{
			return this.calcData;
		}
		
		public function GetAggregationData():Array
		{
			return this.aggreData;
		}
		
		public function GetXLabel():String
		{
			return this.xLabel;
		}
		
		public function GetYLabel():String
		{
			return this.yLabel;
		}
		
		/* Helpers */
		public function ResetMetricData():void
		{
			this._dictArrayValues = false;
			this._dictValues = false;
			this._pipedValues = false;
			this.csData = "";
			
			this.categoryDates = new Array();
			this.categoryValues = new Array();
			this.caKPIMaxData = new Array();
			this.kpiCategory = new Array();
		}
		
		public function ResetCategoryData():void
		{
			this.categoryDates = new Array();
			this.categoryValues = new Array();
			this.kpiCategory = new Array();
		}
		
		public function ResetChartData():void
		{
			this.caKPI_Data = new Array();
			this.kpiData = new Dictionary();
			this._chartArrayValues = false;
			this.aggreData = new Array();
			this.categoryArray = null;
			this.kpiCategory = null;
		}
	}

}