<div class="hoocompanion-mb-desc">
	<# if ( data.label ) { #>
		<span class="butterbean-label">{{ data.label }}</span>
	<# } #>

	<# if ( data.description ) { #>
		<span class="butterbean-description">{{{ data.description }}}</span>
	<# } #>
</div>

<div class="hoocompanion-mb-field">
	<div class="range-wrapper">
		<input type="range" {{{ data.attr }}} value="{{ data.value }}" />
		<input type="number" {{{ data.attr }}} class="hoocompanion-range-input" value="{{ data.value }}" />
		<span class="hoocompanion-reset-slider"><span class="dashicons dashicons-image-rotate"></span></span>
	</div>
</div>