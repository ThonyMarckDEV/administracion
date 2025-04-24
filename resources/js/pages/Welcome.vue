<template>
  <div class="app-container" :class="{ 'dark': isDarkMode }">
    <div class="bg-white dark:bg-gray-950 text-gray-800 dark:text-gray-100 min-h-screen transition-colors duration-500">

     <!-- Header/Hero Section -->
      <section class="relative py-20 lg:py-32 overflow-hidden">
        <div class="container mx-auto px-6 max-w-5xl relative z-10">
          <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-16">
            <div class="w-full lg:w-1/2 space-y-8">
              <div class="inline-flex items-center px-4 py-1.5 rounded-full bg-blue-100/50 dark:bg-green-900/20 text-blue-600 dark:text-blue-400 text-sm font-medium">
                <span class="mr-2">✦</span> Soluciones Tecnológicas
              </div>
              <h1 class="text-5xl lg:text-6xl font-light leading-tight tracking-tight">
                <span class="text-blue-600 dark:text-blue-600 font-normal">T&J </span>
                <span class="text-gray-900 dark:text-white">Administracion</span>
              </h1>
              <p class="text-lg text-gray-600 dark:text-gray-300 font-light leading-relaxed max-w-md">
                Elevamos su negocio con servicios exclusivos de desarrollo, consultoría y soluciones tecnológicas a medida.
              </p>
              <div class="flex flex-wrap gap-5 pt-2 z-20 relative">
                <!-- Botón -->
                <a href="/login" class="block w-full sm:w-auto">
                  <button class="w-full px-8 py-4 sm:py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-light rounded-lg transition duration-300 shadow-lg hover:shadow-xl shadow-blue-600/10 dark:shadow-blue-500/5 active:bg-blue-800 active:scale-95">
                    INICIO
                  </button>
                </a>
              </div>
            </div>
            <div class="w-full lg:w-1/2 relative z-0">
              <!-- Fondos decorativos sin interactividad -->
              <div class="absolute -top-20 -right-20 w-64 h-64 bg-blue-400/10 dark:bg-blue-500/5 rounded-full filter blur-2xl pointer-events-none"></div>
              <div class="absolute -bottom-10 -left-10 w-48 h-48 bg-blue-400/10 dark:bg-blue-500/5 rounded-full filter blur-3xl pointer-events-none"></div>
              <img src="../../js/pages/img/system/dashboard.png" alt="Dashboard T/J Technologies" class="rounded-2xl shadow-2xl w-full relative z-10 border border-gray-100 dark:border-gray-900" />
            </div>
          </div>
        </div>
      </section>

      <!-- System Information Section -->
      <section class="py-24 bg-gray-50 dark:bg-gray-900/20 px-6">
        <div class="container mx-auto max-w-5xl">
          <div class="text-center mb-16 fade-in">
            <span class="inline-block px-4 py-1.5 bg-blue-100/50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-600 text-sm font-medium rounded-full mb-4">Sistema Integral</span>
            <h2 class="text-3xl font-light mb-6 tracking-tight">Plataforma de <span class="text-blue-600 dark:text-blue-600">Administración</span> Avanzada</h2>
            <p class="max-w-2xl mx-auto text-gray-600 dark:text-gray-300 font-light">
              Gestione su infraestructura digital con elegancia y precisión desde un único sistema integrado.
            </p>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div v-for="(feature, index) in systemFeatures" :key="index" class="bg-white dark:bg-gray-900/40 backdrop-blur-sm rounded-2xl shadow-xl dark:shadow-gray-950/50 p-8 system-card fade-in hover:translate-y-[-5px] transition-all duration-300">
              <div class="text-blue-600 dark:text-blue-400 mb-6">
                <i :class="`${feature.icon} text-3xl`"></i>
              </div>
              <h3 class="text-xl font-light mb-4">{{ feature.title }}</h3>
              <p class="text-gray-600 dark:text-gray-300 font-light">{{ feature.description }}</p>
            </div>
          </div>
        </div>
      </section>
  
      <!-- Features Banner Carousel -->
      <section class="py-24 px-6">
        <div class="container mx-auto max-w-5xl">
          <div class="text-center mb-16 fade-in">
            <span class="inline-block px-4 py-1.5 bg-blue-100/50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-600 text-sm font-medium rounded-full mb-4">Servicios Exclusivos</span>
            <h2 class="text-3xl font-light mb-6 tracking-tight">Soluciones <span class="text-blue-600 dark:text-blue-600">Premium</span> para su Empresa</h2>
            <p class="max-w-2xl mx-auto text-gray-600 dark:text-gray-300 font-light">
              Una experiencia tecnológica diseñada para potenciar la excelencia en su negocio
            </p>
          </div>
  
          <!-- Features Carousel -->
          <div class="relative h-[550px] lg:h-[600px] overflow-hidden rounded-2xl shadow-2xl fade-in">
            <div v-for="(slide, index) in featureSlides" :key="index"
                 class="absolute inset-0 transition-all duration-1000 flex flex-col lg:flex-row"
                 :class="currentSlide === index ? 'opacity-100 z-10' : 'opacity-0 z-0'">
              <div :class="`${slide.bgClass} text-white p-12 lg:p-16 flex flex-col justify-center w-full lg:w-1/2`">
                <h3 class="text-3xl font-light mb-8 tracking-tight">{{ slide.title }}</h3>
                <p class="mb-10 text-lg font-light opacity-90">{{ slide.description }}</p>
                <ul class="space-y-5">
                  <li v-for="(item, idx) in slide.features" :key="idx" class="flex items-center">
                    <div class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center mr-4">
                      <i class="fas fa-check text-sm"></i>
                    </div>
                    <span class="font-light">{{ item }}</span>
                  </li>
                </ul>
                <button class="mt-10 px-8 py-3.5 bg-white hover:bg-gray-100 text-blue-900 font-light rounded-lg w-fit transition-all duration-300">
                  Explorar servicio
                </button>
              </div>
              <div class="w-full lg:w-1/2 bg-gray-100 dark:bg-gray-900/40 flex items-center justify-center p-12">
                <img :src="`/api/placeholder/${slide.imgWidth}/${slide.imgHeight}`" :alt="slide.title" class="rounded-xl shadow-xl max-h-full" />
              </div>
            </div>
            
            <!-- Indicators -->
            <div class="absolute bottom-8 left-0 right-0 flex justify-center space-x-4 z-20">
              <button v-for="(_, index) in featureSlides" :key="index"
                      @click="currentSlide = index" 
                      class="w-12 h-1 rounded-sm transition-all duration-300"
                      :class="currentSlide === index ? 'bg-blue-500' : 'bg-gray-300/50 dark:bg-gray-800/50'">
              </button>
            </div>
            
            <!-- Navigation arrows -->
            <button @click="prevSlide" class="absolute left-6 top-1/2 -translate-y-1/2 z-20 w-12 h-12 rounded-full bg-white/80 dark:bg-gray-900/60 backdrop-blur-sm flex items-center justify-center text-gray-800 dark:text-white hover:bg-white/90 dark:hover:bg-gray-900/80 transition-all duration-300 shadow-lg">
              <i class="fas fa-chevron-left"></i>
            </button>
            <button @click="nextSlide" class="absolute right-6 top-1/2 -translate-y-1/2 z-20 w-12 h-12 rounded-full bg-white/80 dark:bg-gray-900/60 backdrop-blur-sm flex items-center justify-center text-gray-800 dark:text-white hover:bg-white/90 dark:hover:bg-gray-900/80 transition-all duration-300 shadow-lg">
              <i class="fas fa-chevron-right"></i>
            </button>
          </div>
        </div>
      </section>
  
      <!-- Technologies Carousel -->
      <section class="py-24 bg-gray-50 dark:bg-gray-900/30 px-6">
        <div class="container mx-auto max-w-5xl">
          <div class="text-center mb-16 fade-in">
            <span class="inline-block px-4 py-1.5 bg-blue-100/50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-600 text-sm font-medium rounded-full mb-4">Tecnologías Exclusivas</span>
            <h2 class="text-3xl font-light mb-6 tracking-tight">Stack <span class="text-blue-600 dark:text-blue-600">Tecnológico</span> Superior</h2>
            <p class="max-w-2xl mx-auto text-gray-600 dark:text-gray-300 font-light">
              Implementamos las herramientas más avanzadas del mercado para garantizar resultados excepcionales
            </p>
          </div>

          <!-- Tech Icons -->
          <div class="relative py-10 overflow-hidden fade-in">
              <div class="flex flex-wrap justify-center gap-10">
                  <div v-for="(tech, index) in technologies" :key="index" class="tech-icon group">
                      <div class="w-28 h-28 bg-white dark:bg-gray-900/40 backdrop-blur-sm rounded-2xl shadow-xl dark:shadow-gray-950/50 flex items-center justify-center mb-4 border border-gray-100 dark:border-gray-800/50 group-hover:border-blue-400 dark:group-hover:border-blue-500 transition-all duration-300">
                          <!-- Mostrar logo inverso al modo actual -->
                          <img :src="isDarkMode ? tech.imgSrcLight : tech.imgSrcDark" :alt="tech.name" class="h-16 w-auto object-contain transition-opacity duration-300" />
                      </div>
                      <div class="text-center text-sm font-light tracking-wide">{{ tech.name }}</div>
                  </div>
              </div>
          </div>

        </div>
      </section>

      <!-- Footer -->
      <footer class="py-12 border-t border-gray-200 dark:border-gray-900/50 px-6">
        <div class="container mx-auto max-w-5xl">
          <div class="flex flex-col md:flex-row justify-between items-center">
            <div class="mb-8 md:mb-0">
              <h3 class="text-2xl font-light mb-2">
                <span class="text-blue-600 dark:text-blue-600">T & J </span> 
                <span class="text-gray-900 dark:text-white">Administracion</span>
              </h3>
              <p class="text-gray-600 dark:text-gray-300 font-light">Excelencia tecnológica a su alcance</p>
            </div>
          </div>
          <div class="border-t border-gray-200 dark:border-gray-900/50 mt-8 pt-8 text-center text-gray-500 dark:text-gray-400 font-light text-sm">
            © 2025 T & J. Todos los derechos reservados.
          </div>
        </div>
      </footer>
    </div>
  </div>
</template>

<script>
  import laravelLogoLight from '../../js/pages/img/logos/light/laravellogolight.png';
  import laravelLogoDark from '../../js/pages/img/logos/dark/laravellogodark.png';
  import laravelCloudLogoLight from '../../js/pages/img/logos/light/laravelcloudlogolight.png';
  import laravelCloudLogoDark from '../../js/pages/img/logos/dark/laravelcloudlogodark.png';
  import vueLogoLight from '../../js/pages/img/logos/light/vuelogolight.png';
  import vueLogoDark from '../../js/pages/img/logos/dark/vuelogodark.png';
  import dockerLogoLight from '../../js/pages/img/logos/light/dockerlogolight.png';
  import dockerLogoDark from '../../js/pages/img/logos/dark/dockerlogodark.png';
  
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
            description: 'Monitoreo inteligente de servicios en tiempo real con visualizaciones interactivas para decisiones estratégicas.'
          },
          {
            icon: 'fas fa-users-cog',
            title: 'Gestión Integral de Clientes',
            description: 'Sistema CRM avanzado para optimizar relaciones comerciales con segmentación personalizada.'
          },
          {
            icon: 'fas fa-server',
            title: 'Administración de Infraestructura',
            description: 'Control centralizado con monitoreo continuo y alertas predictivas para mantener rendimiento óptimo.'
          },
          {
            icon: 'fas fa-shield-alt',
            title: 'Seguridad Empresarial Avanzada',
            description: 'Protección multinivel con encriptación de última generación y autenticación biométrica.'
          },
          {
            icon: 'fas fa-code-branch',
            title: 'Desarrollo y Control de Proyectos',
            description: 'Metodologías ágiles con seguimiento en tiempo real y control de versiones integrado.'
          },
          {
            icon: 'fas fa-file-invoice-dollar',
            title: 'Sistema Financiero Automatizado',
            description: 'Facturación inteligente con múltiples pasarelas de pago y reportes personalizables.'
          }
        ],
        featureSlides: [
          {
            title: 'Desarrollo de Software Exclusivo',
            description: 'Creamos soluciones digitales de alto rendimiento adaptadas perfectamente a los procesos únicos de su organización.',
            bgClass: 'bg-gradient-to-r from-blue-700 to-blue-800 dark:from-blue-800 dark:to-blue-900',
            features: [
              'Aplicaciones empresariales de alto rendimiento',
              'Interfaces elegantes y experiencias de usuario premium',
              'Arquitectura escalable con tecnologías de vanguardia'
            ],
            imgWidth: 600,
            imgHeight: 450
          },
          {
            title: 'Infraestructura Cloud Premium',
            description: 'Plataforma robusta y flexible para sus aplicaciones con monitoreo proactivo y soporte personalizado.',
            bgClass: 'bg-gradient-to-r from-blue-700 to-blue-800 dark:from-blue-800 dark:to-blue-900',
            features: [
              'Entornos optimizados para máximo rendimiento',
              'Arquitectura cloud de última generación',
              'Garantía SLA de nivel empresarial'
            ],
            imgWidth: 600,
            imgHeight: 450
          },
          {
            title: 'Consultoría Estratégica de Elite',
            description: 'Asesoramiento tecnológico por expertos del sector para transformar digitalmente su organización.',
            bgClass: 'bg-gradient-to-r from-indigo-700 to-indigo-800 dark:from-indigo-800 dark:to-indigo-900',
            features: [
              'Análisis profundo de procesos e infraestructura',
              'Estrategia digital personalizada para su sector',
              'Optimización de recursos y retorno de inversión'
            ],
            imgWidth: 600,
            imgHeight: 450
          }
        ],
        technologies: [
            { 
                name: 'Laravel 12', 
                imgSrcLight: laravelLogoLight,
                imgSrcDark: laravelLogoDark,
                colorClass: 'text-red-600' 
            },
            { 
                name: 'Laravel Cloud',
                imgSrcLight: laravelCloudLogoLight,
                imgSrcDark: laravelCloudLogoDark,
                colorClass: 'text-purple-500' 
            },
            { 
                name: 'Vue.js',
                imgSrcLight: vueLogoLight,
                imgSrcDark: vueLogoDark,
                colorClass: 'text-green-500' 
            },
            { 
                name: 'Docker',
                imgSrcLight: dockerLogoLight,
                imgSrcDark: dockerLogoDark,
                colorClass: 'text-blue-500' 
            },
        ],
      }
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