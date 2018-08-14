/* global esdBlockSettings */
const { __ } = wp.i18n;

const {
	InspectorControls,
	PanelColorSettings,
} = wp.editor;

const { SelectControl, PanelBody } = wp.components;

const Controls = ( props ) => {
	const { attributes: { list, formBackgroundColor, formTextColor }, setAttributes } = props;

	return (
		<InspectorControls>
			<PanelBody>
				<SelectControl
					label={ __( 'Mailing List' ) }
					description={ __( 'Choose mailing list to use for the subscription form.' ) }
					value={ list }
					options={ JSON.parse( esdBlockSettings.lists ) }
					onChange={ ( value ) => setAttributes( { list: value } ) }
				/>

				<PanelColorSettings
					title={ __( 'Color Settings' ) }
					colorSettings={ [
						{
							label: __( 'Background Color' ),
							value: formBackgroundColor,
							onChange: ( value ) => setAttributes( { formBackgroundColor: value } ),
						},
						{
							label: __( 'Text Color' ),
							value: formTextColor,
							onChange: ( value ) => setAttributes( { formTextColor: value } ),
						},
					] }
				/>
			</PanelBody>
		</InspectorControls>
	);
};

export default Controls;
