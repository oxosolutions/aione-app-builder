jQuery(document).ready(function () {
    var wptoolset_taxonomy_settings_instances = wptoolset_taxonomy_settings['instances'];
    for(var taxonomy_settings_instance_index in wptoolset_taxonomy_settings_instances){
        var currentTaxonomySettings = wptoolset_taxonomy_settings_instances[taxonomy_settings_instance_index];
        initTaxonomies(currentTaxonomySettings.values, currentTaxonomySettings.name, currentTaxonomySettings.form, currentTaxonomySettings.field);
    }
});