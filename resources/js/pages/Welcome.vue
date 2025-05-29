<template>
    <div class="app-container" :class="{ dark: isDarkMode }">
        <div class="min-h-screen bg-white text-gray-800 transition-colors duration-500 dark:bg-gray-950 dark:text-gray-100">
            <!-- Header/Hero Section -->
            <section class="relative overflow-hidden py-20 lg:py-32">
                <div class="container relative z-10 mx-auto max-w-5xl px-6">
                    <div class="flex flex-col items-center gap-12 lg:flex-row lg:gap-16">
                        <div class="w-full space-y-8 lg:w-1/2">
                            <div
                                class="inline-flex items-center rounded-full bg-blue-100/50 px-4 py-1.5 text-sm font-medium text-blue-600 dark:bg-green-900/20 dark:text-blue-400"
                            >
                                <span class="mr-2">✦</span> Soluciones Tecnológicas
                            </div>
                            <h1 class="text-5xl font-light leading-tight tracking-tight lg:text-6xl">
                                <span class="font-normal text-blue-600 dark:text-blue-600">T&J </span>
                                <span class="text-gray-900 dark:text-white">Administracion</span>
                            </h1>
                            <p class="max-w-md text-lg font-light leading-relaxed text-gray-600 dark:text-gray-300">
                                Elevamos su negocio con servicios exclusivos de desarrollo, consultoría y soluciones tecnológicas a medida.
                            </p>
                            <div class="relative z-20 flex flex-wrap gap-5 pt-2">
                                <!-- Botón -->
                                <a href="/login" class="block w-full sm:w-auto">
                                    <button
                                        class="w-full rounded-lg bg-blue-600 px-8 py-4 font-light text-white shadow-lg shadow-blue-600/10 transition duration-300 hover:bg-blue-700 hover:shadow-xl active:scale-95 active:bg-blue-800 dark:shadow-blue-500/5 sm:py-3.5"
                                    >
                                        INICIO
                                    </button>
                                </a>
                            </div>
                        </div>
                        <div class="relative z-0 w-full lg:w-1/2">
                            <!-- Fondos decorativos sin interactividad -->
                            <div
                                class="pointer-events-none absolute -right-20 -top-20 h-64 w-64 rounded-full bg-blue-400/10 blur-2xl filter dark:bg-blue-500/5"
                            ></div>
                            <div
                                class="pointer-events-none absolute -bottom-10 -left-10 h-48 w-48 rounded-full bg-blue-400/10 blur-3xl filter dark:bg-blue-500/5"
                            ></div>
                            <img
                                src="@public/images/landing/system/dashboard.png"
                                alt="Dashboard T/J Technologies"
                                class="relative z-10 w-full rounded-2xl border border-gray-100 shadow-2xl dark:border-gray-900"
                            />
                        </div>
                    </div>
                </div>
            </section>

            <!-- System Information Section -->
            <section class="bg-gray-50 px-6 py-24 dark:bg-gray-900/20">
                <div class="container mx-auto max-w-5xl">
                    <div class="mb-16 text-center fade-in">
                        <span
                            class="mb-4 inline-block rounded-full bg-blue-100/50 px-4 py-1.5 text-sm font-medium text-blue-600 dark:bg-blue-900/20 dark:text-blue-600"
                            >Sistema Integral</span
                        >
                        <h2 class="mb-6 text-3xl font-light tracking-tight">
                            Plataforma de <span class="text-blue-600 dark:text-blue-600">Administración</span> Avanzada
                        </h2>
                        <p class="mx-auto max-w-2xl font-light text-gray-600 dark:text-gray-300">
                            Gestione su infraestructura digital con elegancia y precisión desde un único sistema integrado.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                        <div
                            v-for="(feature, index) in systemFeatures"
                            :key="index"
                            class="system-card rounded-2xl bg-white p-8 shadow-xl backdrop-blur-sm transition-all duration-300 fade-in hover:translate-y-[-5px] dark:bg-gray-900/40 dark:shadow-gray-950/50"
                        >
                            <div class="mb-6 text-blue-600 dark:text-blue-400">
                                <i :class="`${feature.icon} text-3xl`"></i>
                            </div>
                            <h3 class="mb-4 text-xl font-light">{{ feature.title }}</h3>
                            <p class="font-light text-gray-600 dark:text-gray-300">{{ feature.description }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Features Banner Carousel -->
            <section class="px-6 py-24">
                <div class="container mx-auto max-w-5xl">
                    <div class="mb-16 text-center fade-in">
                        <span
                            class="mb-4 inline-block rounded-full bg-blue-100/50 px-4 py-1.5 text-sm font-medium text-blue-600 dark:bg-blue-900/20 dark:text-blue-600"
                            >Servicios Exclusivos</span
                        >
                        <h2 class="mb-6 text-3xl font-light tracking-tight">
                            Soluciones <span class="text-blue-600 dark:text-blue-600">Premium</span> para su Empresa
                        </h2>
                        <p class="mx-auto max-w-2xl font-light text-gray-600 dark:text-gray-300">
                            Una experiencia tecnológica diseñada para potenciar la excelencia en su negocio
                        </p>
                    </div>

                    <!-- Features Carousel -->
                    <div class="relative h-[550px] overflow-hidden rounded-2xl shadow-2xl fade-in lg:h-[600px]">
                        <div
                            v-for="(slide, index) in featureSlides"
                            :key="index"
                            class="absolute inset-0 flex flex-col transition-all duration-1000 lg:flex-row"
                            :class="currentSlide === index ? 'z-10 opacity-100' : 'z-0 opacity-0'"
                        >
                            <div :class="`${slide.bgClass} flex w-full flex-col justify-center p-12 text-white lg:w-1/2 lg:p-16`">
                                <h3 class="mb-8 text-3xl font-light tracking-tight">{{ slide.title }}</h3>
                                <p class="mb-10 text-lg font-light opacity-90">{{ slide.description }}</p>
                                <ul class="space-y-5">
                                    <li v-for="(item, idx) in slide.features" :key="idx" class="flex items-center">
                                        <div class="mr-4 flex h-6 w-6 items-center justify-center rounded-full bg-white/20">
                                            <i class="fas fa-check text-sm"></i>
                                        </div>
                                        <span class="font-light">{{ item }}</span>
                                    </li>
                                </ul>
                                <button
                                    class="mt-10 w-fit rounded-lg bg-white px-8 py-3.5 font-light text-blue-900 transition-all duration-300 hover:bg-gray-100"
                                >
                                    Explorar servicio
                                </button>
                            </div>
                            <div class="flex w-full items-center justify-center bg-gray-100 p-12 dark:bg-gray-900/40 lg:w-1/2">
                                <img
                                    :src="`/api/placeholder/${slide.imgWidth}/${slide.imgHeight}`"
                                    :alt="slide.title"
                                    class="max-h-full rounded-xl shadow-xl"
                                />
                            </div>
                        </div>

                        <!-- Indicators -->
                        <div class="absolute bottom-8 left-0 right-0 z-20 flex justify-center space-x-4">
                            <button
                                v-for="(_, index) in featureSlides"
                                :key="index"
                                @click="currentSlide = index"
                                class="h-1 w-12 rounded-sm transition-all duration-300"
                                :class="currentSlide === index ? 'bg-blue-500' : 'bg-gray-300/50 dark:bg-gray-800/50'"
                            ></button>
                        </div>

                        <!-- Navigation arrows -->
                        <button
                            @click="prevSlide"
                            class="absolute left-6 top-1/2 z-20 flex h-12 w-12 -translate-y-1/2 items-center justify-center rounded-full bg-white/80 text-gray-800 shadow-lg backdrop-blur-sm transition-all duration-300 hover:bg-white/90 dark:bg-gray-900/60 dark:text-white dark:hover:bg-gray-900/80"
                        >
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button
                            @click="nextSlide"
                            class="absolute right-6 top-1/2 z-20 flex h-12 w-12 -translate-y-1/2 items-center justify-center rounded-full bg-white/80 text-gray-800 shadow-lg backdrop-blur-sm transition-all duration-300 hover:bg-white/90 dark:bg-gray-900/60 dark:text-white dark:hover:bg-gray-900/80"
                        >
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </section>

            <!-- Technologies Carousel -->
            <section class="bg-gray-50 px-6 py-24 dark:bg-gray-900/30">
                <div class="container mx-auto max-w-5xl">
                    <div class="mb-16 text-center fade-in">
                        <span
                            class="mb-4 inline-block rounded-full bg-blue-100/50 px-4 py-1.5 text-sm font-medium text-blue-600 dark:bg-blue-900/20 dark:text-blue-600"
                            >Tecnologías Exclusivas</span
                        >
                        <h2 class="mb-6 text-3xl font-light tracking-tight">
                            Stack <span class="text-blue-600 dark:text-blue-600">Tecnológico</span> Superior
                        </h2>
                        <p class="mx-auto max-w-2xl font-light text-gray-600 dark:text-gray-300">
                            Implementamos las herramientas más avanzadas del mercado para garantizar resultados excepcionales
                        </p>
                    </div>

                    <!-- Tech Icons -->
                    <div class="relative overflow-hidden py-10 fade-in">
                        <div class="flex flex-wrap justify-center gap-10">
                            <div v-for="(tech, index) in technologies" :key="index" class="tech-icon group">
                                <div
                                    class="mb-4 flex h-28 w-28 items-center justify-center rounded-2xl border border-gray-100 bg-white shadow-xl backdrop-blur-sm transition-all duration-300 group-hover:border-blue-400 dark:border-gray-800/50 dark:bg-gray-900/40 dark:shadow-gray-950/50 dark:group-hover:border-blue-500"
                                >
                                    <!-- Mostrar logo inverso al modo actual -->
                                    <img
                                        :src="isDarkMode ? tech.imgSrcLight : tech.imgSrcDark"
                                        :alt="tech.name"
                                        class="h-16 w-auto object-contain transition-opacity duration-300"
                                    />
                                </div>
                                <div class="text-center text-sm font-light tracking-wide">{{ tech.name }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Footer -->
            <footer class="border-t border-gray-200 px-6 py-12 dark:border-gray-900/50">
                <div class="container mx-auto max-w-5xl">
                    <div class="flex flex-col items-center justify-between md:flex-row">
                        <div class="mb-8 md:mb-0">
                            <h3 class="mb-2 text-2xl font-light">
                                <span class="text-blue-600 dark:text-blue-600">T & J </span>
                                <span class="text-gray-900 dark:text-white">Administracion</span>
                            </h3>
                            <p class="font-light text-gray-600 dark:text-gray-300">Excelencia tecnológica a su alcance</p>
                        </div>
                    </div>
                    <div
                        class="mt-8 border-t border-gray-200 pt-8 text-center text-sm font-light text-gray-500 dark:border-gray-900/50 dark:text-gray-400"
                    >
                        © 2025 T & J. Todos los derechos reservados.
                    </div>
                </div>
            </footer>
        </div>
    </div>
</template>

<script lang="ts">
import dockerLogoDark from '@public/images/landing/logos/dark/dockerlogodark.png';
import laravelCloudLogoDark from '@public/images/landing/logos/dark/laravelcloudlogodark.png';
import laravelLogoDark from '@public/images/landing/logos/dark/laravellogodark.png';
import vueLogoDark from '@public/images/landing/logos/dark/vuelogodark.png';
import dockerLogoLight from '@public/images/landing/logos/light/dockerlogolight.png';
import laravelCloudLogoLight from '@public/images/landing/logos/light/laravelcloudlogolight.png';
import laravelLogoLight from '@public/images/landing/logos/light/laravellogolight.png';
import vueLogoLight from '@public/images/landing/logos/light/vuelogolight.png';

export default {
    name: 'WelcomeView',
    data() {
        return {
            isDarkMode: false,
            currentSlide: 0,
            systemFeatures: [
                {
                    icon: 'fas fa-chart-line',
                    title: 'Analytics de Alto Rendimiento',
                    description: 'Monitoreo inteligente de servicios en tiempo real con visualizaciones interactivas para decisiones estratégicas.',
                },
                {
                    icon: 'fas fa-users-cog',
                    title: 'Gestión Integral de Clientes',
                    description: 'Sistema CRM avanzado para optimizar relaciones comerciales con segmentación personalizada.',
                },
                {
                    icon: 'fas fa-server',
                    title: 'Administración de Infraestructura',
                    description: 'Control centralizado con monitoreo continuo y alertas predictivas para mantener rendimiento óptimo.',
                },
                {
                    icon: 'fas fa-shield-alt',
                    title: 'Seguridad Empresarial Avanzada',
                    description: 'Protección multinivel con encriptación de última generación y autenticación biométrica.',
                },
                {
                    icon: 'fas fa-code-branch',
                    title: 'Desarrollo y Control de Proyectos',
                    description: 'Metodologías ágiles con seguimiento en tiempo real y control de versiones integrado.',
                },
                {
                    icon: 'fas fa-file-invoice-dollar',
                    title: 'Sistema Financiero Automatizado',
                    description: 'Facturación inteligente con múltiples pasarelas de pago y reportes personalizables.',
                },
            ],
            featureSlides: [
                {
                    title: 'Desarrollo de Software Exclusivo',
                    description: 'Creamos soluciones digitales de alto rendimiento adaptadas perfectamente a los procesos únicos de su organización.',
                    bgClass: 'bg-gradient-to-r from-blue-700 to-blue-800 dark:from-blue-800 dark:to-blue-900',
                    features: [
                        'Aplicaciones empresariales de alto rendimiento',
                        'Interfaces elegantes y experiencias de usuario premium',
                        'Arquitectura escalable con tecnologías de vanguardia',
                    ],
                    imgWidth: 600,
                    imgHeight: 450,
                },
                {
                    title: 'Infraestructura Cloud Premium',
                    description: 'Plataforma robusta y flexible para sus aplicaciones con monitoreo proactivo y soporte personalizado.',
                    bgClass: 'bg-gradient-to-r from-blue-700 to-blue-800 dark:from-blue-800 dark:to-blue-900',
                    features: [
                        'Entornos optimizados para máximo rendimiento',
                        'Arquitectura cloud de última generación',
                        'Garantía SLA de nivel empresarial',
                    ],
                    imgWidth: 600,
                    imgHeight: 450,
                },
                {
                    title: 'Consultoría Estratégica de Elite',
                    description: 'Asesoramiento tecnológico por expertos del sector para transformar digitalmente su organización.',
                    bgClass: 'bg-gradient-to-r from-indigo-700 to-indigo-800 dark:from-indigo-800 dark:to-indigo-900',
                    features: [
                        'Análisis profundo de procesos e infraestructura',
                        'Estrategia digital personalizada para su sector',
                        'Optimización de recursos y retorno de inversión',
                    ],
                    imgWidth: 600,
                    imgHeight: 450,
                },
            ],
            technologies: [
                {
                    name: 'Laravel 12',
                    imgSrcLight: laravelLogoLight,
                    imgSrcDark: laravelLogoDark,
                    colorClass: 'text-red-600',
                },
                {
                    name: 'Laravel Cloud',
                    imgSrcLight: laravelCloudLogoLight,
                    imgSrcDark: laravelCloudLogoDark,
                    colorClass: 'text-purple-500',
                },
                {
                    name: 'Vue.js',
                    imgSrcLight: vueLogoLight,
                    imgSrcDark: vueLogoDark,
                    colorClass: 'text-green-500',
                },
                {
                    name: 'Docker',
                    imgSrcLight: dockerLogoLight,
                    imgSrcDark: dockerLogoDark,
                    colorClass: 'text-blue-500',
                },
            ],
        };
    },
    mounted() {
        // Autoplay for feature slides
        this.startAutoplay();
        
        // Check for cookie first
        const savedMode = this.getCookie('appearance');
        if (savedMode) {
            this.isDarkMode = savedMode === 'dark';
        }
        
        // Add animation classes
        this.animateOnScroll();
        window.addEventListener('scroll', this.animateOnScroll);
    },
    methods: {
        // Cookie management methods
        setCookie(name, value, days) {
            let expires = '';
            if (days) {
                const date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = '; expires=' + date.toUTCString();
            }
            document.cookie = name + '=' + (value || '') + expires + '; path=/; SameSite=Strict';
        },
        
        getCookie(name) {
            const nameEQ = name + '=';
            const ca = document.cookie.split(';');
            for (let i = 0; i < ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) === ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
            }
            return null;
        },
        
        nextSlide() {
            this.currentSlide = (this.currentSlide + 1) % this.featureSlides.length;
        },
        prevSlide() {
            this.currentSlide = (this.currentSlide - 1 + this.featureSlides.length) % this.featureSlides.length;
        },
        startAutoplay() {
            this.autoplayInterval = setInterval(() => {
                this.nextSlide();
            }, 6000);
        },
        animateOnScroll() {
            const elements = document.querySelectorAll('.fade-in');
            elements.forEach(element => {
                const elementTop = element.getBoundingClientRect().top;
                const elementVisible = 150;
                
                if (elementTop < window.innerHeight - elementVisible) {
                    element.classList.add('is-visible');
                }
            });
        }
    }
  }
</script>

<style scoped>
/* Base styles */
.app-container {
  font-family: 'Inter', sans-serif;
}

/* Dark mode */
.dark {
  color-scheme: dark;
}

/* Animation styles */
.fade-in {
  opacity: 0;
  transform: translateY(30px);
  transition: opacity 1s ease, transform 1s ease;
}

.fade-in.is-visible {
  opacity: 1;
  transform: translateY(0);
}

/* Stagger children animation */
.system-card:nth-child(1) {
  transition-delay: 0.1s;
}
.system-card:nth-child(2) {
  transition-delay: 0.2s;
}
.system-card:nth-child(3) {
  transition-delay: 0.3s;
}
.system-card:nth-child(4) {
  transition-delay: 0.4s;
}
.system-card:nth-child(5) {
  transition-delay: 0.5s;
}
.system-card:nth-child(6) {
  transition-delay: 0.6s;
}

/* Tech icon hover effect */
.tech-icon {
  transition: all 0.4s ease;
}

.tech-icon:hover {
  transform: translateY(-8px);
}

/* Custom transition for carousel */
.slide-leave-active,
.slide-enter-active {
  transition: all 1s;
}
.slide-enter {
  transform: translateX(100%);
  opacity: 0;
}
.slide-leave-to {
  transform: translateX(-100%);
  opacity: 0;
}
</style>
