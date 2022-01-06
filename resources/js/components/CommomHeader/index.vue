<template>
    <div>
        <b-navbar class="header" id="header">
            <b-navbar-nav>
                <b-nav-item @click="$router.back()">
                    <span class="text-white">
                        <b-icon icon="chevron-left"></b-icon>
                        Voltar
                    </span>
                </b-nav-item>
            </b-navbar-nav>
            <b-navbar-nav class="ml-auto">
                <b-nav-item>
                    <span class="text-white">
                        Ellegance Ballet
                    </span>
                </b-nav-item>
            </b-navbar-nav>
        </b-navbar>
        <slot/>
    </div>
    
</template>