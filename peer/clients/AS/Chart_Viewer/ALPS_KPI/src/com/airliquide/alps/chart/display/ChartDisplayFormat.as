package com.airliquide.alps.chart.display 
{
	// FLEX SDK
	import mx.charts.*;
	import mx.charts.chartClasses.Series;
	import mx.collections.ArrayCollection;
	import mx.core.*;
	
	// Yahoo SDK
	//import com.yahoo.astra.fl.charts.*;
	
	
	//--------------------------------------
	//  Styles
	//--------------------------------------
	
	/**
     * The padding that separates the border of the component from its contents,
     * in pixels.
     *
     * @default 10
     */
    [Style(name="contentPadding", type="Number")]
	
	/**
     * Name of the class to use as the skin for the background and border of the
     * component.
     *
     * @default ChartBackgroundSkin
     */
    [Style(name="backgroundSkin", type="Class")]
	
	/**
     * The default colors for each series. These colors are used for markers,
     * in most cases, but they may apply to lines, fills, or other graphical
     * items.
     * 
     * <p>An Array of values that correspond to series indices in the data
     * provider. If the number of values in the Array is less than the number
     * of series, then the next series will restart at index zero in the style
     * Array. If the value of this style is an empty Array, then each individual series
     * will use the default or modified value set on the series itself.</p> 
     *
     * <p>Example: If the seriesColors style is equal to [0xffffff, 0x000000] and there
     * are three series in the chart's data provider, then the series at index 0
     * will have a color of 0xffffff, index 1 will have a color of 0x000000, and
     * index 2 will have a color of 0xffffff (starting over from the beginning).</p>
     * 
     * @default [0x00b8bf, 0x8dd5e7, 0xedff9f, 0xffa928, 0xc0fff6, 0xd00050, 0xc6c6c6, 0xc3eafb, 0xfcffad, 0xcfff83, 0x444444, 0x4d95dd, 0xb8ebff, 0x60558f, 0x737d7e, 0xa64d9a, 0x8e9a9b, 0x803e77]
     */
    [Style(name="seriesColors", type="Array")]
	
	/**
     * The default size of the markers in pixels. The actual drawn size of the
     * markers could end up being different in some cases. For example, bar charts
     * and column charts display markers side-by-side, and a chart may need to make
     * the bars or columns smaller to fit within the required region.
     *
     * <p>An Array of values that correspond to series indices in the data
     * provider. If the number of values in the Array is less than the number
     * of series, then the next series will restart at index zero in the style
     * Array. If the value of this style is an empty Array, then each individual series
     * will use the default or modified value set on the series itself.</p>
     * 
     * <p>Example: If the seriesMarkerSizes style is equal to [10, 15] and there
     * are three series in the chart's data provider, then the series at index 0
     * will have a marker size of 10, index 1 will have a marker size of 15, and
     * index 2 will have a marker size of 10 (starting over from the beginning).</p>
     * 
     * @default []
     */
    [Style(name="seriesMarkerSizes", type="Array")]
	
	/**
     * An Array containing the default skin classes for each series. These classes
     * are used to instantiate the marker skins. The values may be fully-qualified
     * package and class strings or a reference to the classes themselves.
     *
     * <p>An Array of values that correspond to series indices in the data
     * provider. If the number of values in the Array is less than the number
     * of series, then the next series will restart at index zero in the style
     * Array. If the value of this style is an empty Array, then each individual series
     * will use the default or modified value set on the series itself.</p> 
     * 
     * <p>Example: If the seriesMarkerSkins style is equal to [CircleSkin, DiamondSkin] and there
     * are three series in the chart's data provider, then the series at index 0
     * will have a marker skin of CircleSkin, index 1 will have a marker skin of DiamondSkin, and
     * index 2 will have a marker skin of CircleSkin (starting over from the beginning).</p>
     * 
     * @default []
     */
    [Style(name="seriesMarkerSkins", type="Array")]
	
	/**
	 * The TextFormat object to use to render data tips.
     *
     * @default TextFormat("_sans", 11, 0x000000, false, false, false, '', '', TextFormatAlign.LEFT, 0, 0, 0, 0)
     */
    [Style(name="dataTipTextFormat", type="TextFormat")]
	
	/**
     * Name of the class to use as the skin for the background and border of the
     * chart's data tip.
     *
     * @default ChartDataTipBackground
     */
    [Style(name="dataTipBackgroundSkin", type="Class")]
	
	/**
	 * If the datatip's content padding is customizable, it will use this value.
	 * The padding that separates the border of the component from its contents,
     * in pixels.
     *
     * @default 6
     */
    [Style(name="dataTipContentPadding", type="Number")]
	
	/**
	 * Determines if data changes should be displayed with animation.
     *
     * @default true
     */
    [Style(name="animationEnabled", type="Boolean")]
	
	/**
	 * Indicates whether embedded font outlines are used to render the text
	 * field. If this value is true, Flash Player renders the text field by
	 * using embedded font outlines. If this value is false, Flash Player
	 * renders the text field by using device fonts.
	 * 
	 * If you set the embedFonts property to true for a text field, you must
	 * specify a font for that text by using the font property of a TextFormat
	 * object that is applied to the text field. If the specified font is not
	 * embedded in the SWF file, the text is not displayed.
	 * 
	 * @default false
     */
    [Style(name="embedFonts", type="Boolean")]
	
	/**
	 * ...
	 * @author earl.lawrence
	 */
	public class ChartDisplayFormat extends  UIComponent
	{
		
		public var RenderEngine:int;
		
		private var seriesData:Series;
		private var stringData:String;
		private var arrayData:Array;
		private var acData:ArrayCollection;
		private var acTotalData:ArrayCollection;
		
		public function ChartDisplayFormat() 
		{
			super();
			
		}
		
		public function SetRenderrer(r:int):void
		{
			RenderEngine = r;
		}
		
		public function SetStringData(data:String):void
		{
			this.stringData = data;
		}
		
		public function SetArrayData(data:Array):void
		{
			this.arrayData = data;
		}
		
		public function SetSeriesData(data:Series):void
		{
			this.seriesData = data;
		}
		
		public function SetArrayCollectionData(data:ArrayCollection):void
		{
			this.acData = data;
		}
		
		public function SetTotalDataDatesAndValues(data:ArrayCollection):void
		{
			this.acTotalData = data;
		}
		
		public function GetArrayData():Array
		{
			return arrayData;
		}
		
		public function GetArrayCollectionData():ArrayCollection
		{
			return acData;
		}
		
		public function GetTotalDateAndValueCollectionData():ArrayCollection
		{
			
			return this.acTotalData;
		}
		
	}

}