import template from './frosh-auto-keyword-linker-index.html.twig';
import './frosh-auto-keyword-linker-index.scss';

const { Component, Mixin } = Shopware;
const { Criteria } = Shopware.Data;

Component.register('frosh-auto-keyword-linker-index', {
    template,
    mixins: [
        Mixin.getByName('listing'),
    ],
    inject: [
        'repositoryFactory',
        'acl',
        'filterFactory',
    ],
    data() {
        return {
            keywords: [],
            sortBy: 'orderDateTime',
            sortDirection: 'DESC',
            isLoading: false,
            filterLoading: false,
            showDeleteModal: false,
            filterCriteria: [],
        }
    },
    created() {
        this.createdComponent();
    },
    computed: {
        froshKeywordsRepository() {
            return this.repositoryFactory.create('frosh_keywords');
        },
        keywordColumns() {
            return this.getKeywordColumns();
        }
    },
    methods: {
        createdComponent() {
            this.filterLoading = true;

            return this.froshKeywordsRepository.search(new Criteria()).then(( result ) => {
                this.filterLoading = false;

                this.keywords = result;
            }).catch(() => {
                this.filterLoading = false;
            });
        },
        getKeywordColumns() {
            return [{
                property: 'id',
                label: 'id',
                //routerLink: 'sw.order.detail',
                allowResize: true,
                primary: true,
            }, {
                property: 'keyword',
                label: 'keyword',
                allowResize: true,
            }
            ];
        }
    }
});
