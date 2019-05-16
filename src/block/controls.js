/* global esdBlockSettings */
const { __ } = wp.i18n;

const { InspectorControls, PanelColorSettings } = wp.editor;

const { SelectControl, PanelBody, ToggleControl } = wp.components;

const Controls = props => {
	const {
		attributes: { list, formBackgroundColor, formTextColor, name, gdpr },
		setAttributes,
	} = props;

	return (
		<InspectorControls>
			{ esdBlockSettings.lists !== 'false' && (
				<PanelBody title={ __( 'Form Settings' ) } initialOpen={ true }>
					<SelectControl
						label={ __( 'Mailing List' ) }
						description={ __(
							'Choose mailing list to use for the subscription form.'
						) }
						value={ list }
						options={ JSON.parse( esdBlockSettings.lists ) }
						onChange={ value => setAttributes( { list: value } ) }
					/>

					<ToggleControl
						label={ __( 'Display Name Field' ) }
						checked={ !! name }
						help={ __( 'Optionally, display name field in the form' ) }
						onChange={ () => setAttributes( { name: ! name } ) }
					/>
					<ToggleControl
						label={ __( 'Display GDPR Field' ) }
						checked={ !! gdpr }
						help={ __( 'Optionally, display GDPR field in the form' ) }
						onChange={ () => setAttributes( { gdpr: ! gdpr } ) }
					/>
				</PanelBody>
			) }

			<PanelColorSettings
				title={ __( 'Color Settings' ) }
				colorSettings={ [
					{
						label: __( 'Background Color' ),
						value: formBackgroundColor,
						onChange: value =>
							setAttributes( { formBackgroundColor: value } ),
					},
					{
						label: __( 'Text Color' ),
						value: formTextColor,
						onChange: value =>
							setAttributes( { formTextColor: value } ),
					},
				] }
			/>
		</InspectorControls>
	);
};

export default Controls;
