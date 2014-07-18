<?xml version="1.0" encoding="UTF-8"?>
<sld:StyledLayerDescriptor xmlns="http://www.opengis.net/sld" xmlns:sld="http://www.opengis.net/sld" xmlns:ogc="http://www.opengis.net/ogc" xmlns:gml="http://www.opengis.net/gml" version="1.0.0">
  <sld:NamedLayer>
    <sld:Name>Layer</sld:Name>
    <sld:UserStyle>
      <sld:Name>cell_type_splits</sld:Name>
      <sld:FeatureTypeStyle>
        <sld:Name>name</sld:Name>
		<sld:Rule>
          <sld:Name>cdma</sld:Name>
          <sld:Title>CDMA</sld:Title>
          <ogc:Filter>
            <ogc:PropertyIsEqualTo>
              <ogc:PropertyName>technology</ogc:PropertyName>
              <ogc:Literal>cdma</ogc:Literal>
            </ogc:PropertyIsEqualTo>
          </ogc:Filter>
         <sld:PointSymbolizer>
         <sld:Graphic>
           <sld:Mark>
             <sld:WellKnownName>circle</sld:WellKnownName>
             <sld:Fill>
               <sld:CssParameter name="fill">#009900</sld:CssParameter>
             </sld:Fill>
           </sld:Mark>
           <sld:Size>20</sld:Size>
         </sld:Graphic>
        </sld:PointSymbolizer>
        </sld:Rule>
		<sld:Rule>
          <sld:Name>evdo</sld:Name>
          <sld:Title>EVDO</sld:Title>
          <ogc:Filter>
            <ogc:PropertyIsEqualTo>
              <ogc:PropertyName>technology</ogc:PropertyName>
              <ogc:Literal>evdo</ogc:Literal>
            </ogc:PropertyIsEqualTo>
          </ogc:Filter>
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

