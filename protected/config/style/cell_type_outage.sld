<?xml version="1.0" encoding="UTF-8"?>
<sld:StyledLayerDescriptor xmlns="http://www.opengis.net/sld" xmlns:sld="http://www.opengis.net/sld" xmlns:ogc="http://www.opengis.net/ogc" xmlns:gml="http://www.opengis.net/gml" version="1.0.0">
  <sld:NamedLayer>
    <sld:Name>Layer</sld:Name>
    <sld:UserStyle>
      <sld:Name>cell_type_outage</sld:Name>
      <sld:FeatureTypeStyle>
        <sld:Name>name</sld:Name>
        <sld:Rule>
          <sld:Title>Outage</sld:Title>
		<sld:PointSymbolizer>
         <sld:Graphic>
           <sld:Mark>
             <sld:WellKnownName>circle</sld:WellKnownName>
             <sld:Fill>
               <sld:CssParameter name="fill">#ff0000</sld:CssParameter>
             </sld:Fill>
           </sld:Mark>
           <sld:Size>25</sld:Size>
         </sld:Graphic>
        </sld:PointSymbolizer>
        </sld:Rule>
		<sld:Rule>
          <sld:Title>Outage</sld:Title>
          <sld:PointSymbolizer>
         <sld:Graphic>
           <sld:Mark>
             <sld:WellKnownName>circle</sld:WellKnownName>
             <sld:Fill>
               <sld:CssParameter name="fill">#ff0000</sld:CssParameter>
             </sld:Fill>
           </sld:Mark>
           <sld:Size>25</sld:Size>
         </sld:Graphic>
        </sld:PointSymbolizer>
        </sld:Rule>
      </sld:FeatureTypeStyle>
    </sld:UserStyle>
  </sld:NamedLayer>
</sld:StyledLayerDescriptor>

