<div class="hoocompanion-mb-desc">
	<# if ( data.label ) { #>
		<span class="butterbean-label">{{ data.label }}</span>
	<# } #>

	<# if ( data.description ) { #>
		<span class="butterbean-description">{{{ data.description }}}</span>
	<# } #>
</div>

<div class="hoocompanion-mb-field">

 <div class="form-group">

                        <div class="input-group">
                            <input data-placement="bottomRight" name="{{ data.field_name }}"  class="form-control icp icp-auto" value="{{ data.value }}" {{{ data.attr }}} type="text"/>
                            <span class="input-group-addon"></span>
                        </div>
                    </div>
                    
</div>