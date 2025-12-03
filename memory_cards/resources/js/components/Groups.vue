<template>
<MemoryCardCurrentLang
    :langs="langs"
    :current-lang="currentLang"
    @change="changeLang"/>

<div class="d-flex mt-3 justify-content-center">
    <ul class="nav nav-tabs app-nav" id="groupsNav" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link"
               :class="addTabClasses"
               id="add-group-tab"
               data-bs-toggle="tab"
               href="#add-group"
               role="tab"
               aria-controls="add-group"
               :aria-selected="reactiveGroups.length === 0">
                {{ $trans.add_group }}
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link"
               :class="groupTabClasses"
               id="groups-tab"
               data-bs-toggle="tab"
               href="#groups"
               role="tab"
               aria-controls="groups"
               aria-selected="false">
                {{ $trans.groups }}
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link"
               id="group-move-tab"
               data-bs-toggle="tab"
               href="#group-move"
               role="tab"
               aria-controls="group-move"
               aria-selected="false">
                {{ $trans.group_move }}
            </a>
        </li>
    </ul>
</div>
<div class="tab-content p-3" id="myTabContent">
    <div class="tab-pane fade"
         :class="addTabClasses"
         id="add-group" role="tabpanel"
         aria-labelledby="add-group-tab"
    >
        <MemoryCardNewGroup @save="addGroup" @blur="clearMessage"/>
    </div>
    <div class="tab-pane fade"
         :class="groupTabClasses"
         id="groups"
         role="tabpanel"
         aria-labelledby="groups-tab"
    >
        <form v-if="reactiveGroups.length > 0" @submit.prevent="saveGroups" class="g-3 justify-content-center mt-3">
            <div class="row mt-3 justify-content-center" v-for="(group, index) in reactiveGroups" :key="index">
                <div class="col-auto col-xs-12 d-flex align-items-center">
                    <input v-model="group.name"
                           type="text"
                           class="form-control text-white bg-transparent me-3"
                           placeholder="{{ $trans.name_group }}"
                           required
                    >

                    <a href="#" class="text-danger" @click="openConfirm(index)">
                        <i class="bi bi-x-circle"></i>
                    </a>
                </div>
            </div>

            <div class="row mt-3 justify-content-center">
                <div class="col-auto col-xs-12">
                    <button @blur="clearMessage" type="submit" class="form-control btn btn-outline-success">
                        {{ $trans.save }}
                    </button>
                </div>
            </div>
        </form>
    </div>
    <div class="tab-pane fade show" id="group-move" role="tabpanel" aria-labelledby="group-move-tab">
        <div class="text-center desc-text mt-3">{{ $trans.card_move }}</div>
        <form @submit.prevent="groupMove" class="row g-3 justify-content-center mt-2">
            <div class="col-auto col-xs-12">
                <SelectGroup :current-group="currentGroup" :groups="reactiveGroups" @change="setGroupFrom" />
            </div>
            <div class="col-auto col-xs-12 d-flex align-items-center justify-content-center">
                <i class="bi bi-arrow-right fs-3 text-light"></i>
            </div>
            <div class="col-auto col-xs-12">
                <SelectGroup :current-group="0" :groups="reactiveGroups" @change="setGroupTo" />
            </div>
            <div class="col-auto col-xs-12">
                <button @blur="clearMessage" type="submit" class="form-control btn btn-outline-success">
                    {{ $trans.save }}
                </button>
            </div>
        </form>
    </div>
    <div class="row justify-content-center mt-3" v-if="message.success || message.error">
        <div v-show="message.success" class="col-auto col-xs-12">
            <div class="btn btn-outline-success w-100" role="alert">
                {{ message.success }}
            </div>
        </div>
        <div v-show="message.error" class="col-auto col-xs-12">
            <div class="btn btn-outline-danger w-100" role="alert">
                {{ message.error }}
            </div>
        </div>
    </div>
</div>

<memory-card-confirm
    :itemName="removeName"
    :question="$trans.cards_whithout_groups"
    :open="showConfirm"
    @confirm="removeGroup"
/>

</template>

<script>
import MemoryCardNewGroup from './NewGroup.vue';
import SelectGroup from './SelectGroup.vue';
import MemoryCardCurrentLang from './CurrentLang.vue';
export default {
    components: {
        MemoryCardNewGroup, MemoryCardCurrentLang, SelectGroup
    },
    props: ['groups', 'currentLang', 'currentGroup', 'langs'],
    data() {
        return {
            reactiveGroups: this.groups || [],
            reactiveLang: this.currentLang,
            removeKey: -1,
            removeName: '',
            newGroup: '',
            message: {},
            messageNew: {},
            groupFrom: this.currentGroup || 0,
            groupTo: 0,
        };
    },
    computed: {
        showConfirm() {
            return this.removeKey > -1;
        },
        addTabClasses() {
            return {
                'show': this.groups.length === 0,
                'active': this.groups.length === 0
            };
        },
        groupTabClasses() {
            return {
                'show': this.groups.length > 0,
                'active': this.groups.length > 0
            };
        },
    },
    methods: {
        groupMove() {
            if (!this.groupFrom || !this.groupTo) {
                this.message.error = this.$trans.select_group;
                return false;
            }
            this.$request('/groups/move', {
                from: this.groupFrom,
                to: this.groupTo,
                group_app: this.groupTo,
            }, 'post').then(response => {
                this.message = response.message;
                this.$request('/groups/set/', {group_qty: this.groupFrom}, 'post');
                this.$request('/groups/set/', {group_qty: this.groupTo}, 'post');
                this.changeGroupQty(this.groupFrom, this.groupTo);
            });
            return false;
        },
        changeGroupQty(from, to) {
            let fromQty = 0;
            this.reactiveGroups.forEach((group, index) => {
                if (group.id == from) {
                    fromQty = group.qty;
                    this.reactiveGroups[index].qty = 0;
                }
            });
            this.reactiveGroups.forEach((group, index) => {
                if (group.id == to) {
                    this.reactiveGroups[index].qty += fromQty;
                }
            });
        },
        setGroupFrom(group) {
            this.groupFrom = group;
        },
        setGroupTo(group) {
            this.groupTo = group;
        },
        openConfirm(key) {
            this.clearMessage();
            this.removeName = this.reactiveGroups[key].name;
            this.removeKey = key;
        },
        removeGroup() {
            this.$request('/groups/remove/' + this.reactiveGroups[ this.removeKey ].id, {});
            this.reactiveGroups.splice(this.removeKey, 1);
            this.closeConfirm();
        },
        closeConfirm() {
            this.removeKey = -1;
        },
        saveGroups() {
            this.reactiveGroups.forEach(group => {
                this.$request('/groups/save/' + group.id, {
                    name: group.name,
                }, 'post').then(response => {
                    this.message = response.message;
                });
            });
        },
        addGroup(name) {
            this.$request('/groups/new', {
                name: name,
                qty: 0,
            }, 'post').then(response => {
                this.message = response.message;
                let group = response.options;
                this.reactiveGroups.push(group);
                this.$request('/groups/set/', {group_app: group.id}, 'post');
            });
        },
        changeLang(lang) {
            this.$request('/langs/set', {
                lang_app: lang,
            }, 'post').then(() => {
                location.reload();
            });
        },
        clearMessage() {
            this.message.success = '';
            this.message.error = '';
        },
    }
};
</script>
