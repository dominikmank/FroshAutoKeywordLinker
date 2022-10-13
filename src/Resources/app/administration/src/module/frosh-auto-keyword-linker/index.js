import './component/frosh-auto-keyword-linker-index';

const {Module} = Shopware;

Module.register('frosh-auto-keyword-linker', {
    type: 'plugin',
    title: 'FroshAutoKeywordLinker.general.title',
    description: 'FroshAutoKeywordLinker.general.description',
    color: '#F88962',
    icon: 'default-avatar-multiple',
    entity: 'frosh_keywords',

    routes: {
        index: {
            component: 'frosh-auto-keyword-linker-index',
            path: 'index'
        }
    },

    navigation: [{
        id: 'frosh-auto-keyword-linker',
        label: 'FroshAutoKeywordLinker.general.title',
        color: '#F88962',
        icon: 'default-avatar-multiple',
        parent: 'sw-catalogue',
        path: 'frosh.auto.keyword.linker.index',
        position: 40
    }],

})
