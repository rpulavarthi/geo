<?xml version="1.0" encoding="UTF-8"?>
<sld:StyledLayerDescriptor xmlns="http://www.opengis.net/sld" xmlns:sld="http://www.opengis.net/sld" xmlns:ogc="http://www.opengis.net/ogc" xmlns:gml="http://www.opengis.net/gml" version="1.0.0">
  <sld:NamedLayer>
    <sld:Name>Layer</sld:Name>
    <sld:UserStyle>
      <sld:Name>cell_type_evdo_na</sld:Name>
      <sld:FeatureTypeStyle>
        <sld:Name>name</sld:Name>
		
		<sld:Rule>
          <sld:Name>BTS Status</sld:Name>
          <sld:Title>BTS Status</sld:Title>
          <ogc:Filter>
            <ogc:PropertyIsEqualTo>
              <ogc:PropertyName>bts_status</ogc:PropertyName>
              <ogc:Literal>RETIRED</ogc:Literal>
            </ogc:PropertyIsEqualTo>
          </ogc:Filter>
         <sld:PointSymbolizer>
         <sld:Graphic>
           <sld:Mark>
             <sld:WellKnownName>circle</sld:WellKnownName>
             <sld:Fill>
               <sld:CssParameter name="fill">#000000</sld:CssParameter>
             </sld:Fill>
           </sld:Mark>
           <sld:Size>20</sld:Size>
         </sld:Graphic>
        </sld:PointSymbolizer>
        </sld:Rule>
		
        <sld:Rule>
          <sld:Title>EVDO_NA</sld:Title>
		<sld:PointSymbolizer>
         <sld:Graphic>
           <sld:Mark>
             <sld:WellKnownName>circle</sld:WellKnownName>
             <sld:Fill>
               <sld:CssParameter name="fill">#646464</sld:CssParameter>
             </sld:Fill>
           </sld:Mark>
           <sld:Size>20</sld:Size>
         </sld:Graphic>
        </sld:PointSymbolizer>
        </sld:Rule>
		<sld:Rule>
          <sld:Title>EVDO_NA</sld:Title>
          <sld:PointSymbolizer>
         <sld:Graphic>
           <sld:Mark>
             <sld:WellKnownName>circle</sld:WellKnownName>
             <sld:Fill>
               <sld:CssParameter name="fill">#646464</sld:CssParameter>
             </sld:Fill>
           </sld:Mark>
           <sld:Size>20</sld:Size>
         </sld:Graphic>
        </sld:PointSymbolizer>
        </sld:Rule>
      </sld:FeatureTypeStyle>
    </sld:UserStyle>
  </sld:NamedLayer>
</sld:StyledLayerDescriptor>

