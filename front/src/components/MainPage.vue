<template>
  <div class="dashboard">
    <div class="container" id="container">
      <div class="expandButton" @click="toggleActive">
        <svg class="dashboard__icon" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">-->
          <path d="M14 8H8V14H6V8H0V6H6V0H8V6H14V8Z" fill="white"/>
        </svg>
      </div>
      <div
          v-for="animalKind in animal_kinds"
          :key="animalKind.id"
          class="item"
          :class="isExist(animalKind) ? 'disabled' : ''"
          @click="isExist(animalKind) ? toGetOld(animalKind.id) : createAnimal(animalKind.id)"
      >
        <img class="icon" :src="getImgUrl(animalKind.kind)">
      </div>
    </div>
    <div class="content">
      <div class="animals">
        <img
            v-for="animal in animals"
            :key="animal.id"
            style="width: 30px"
            class="animal"
            :src="getImgUrl(animal.animal_kind.kind + '_big')"
            :id="animal.animal_kind.kind"
            @click="getInfo(animal.id)"
        >
      </div>
      <div class="info" v-if="info">
        <span>{{
            info.animal_kind.kind === 'cat'
              ? 'Кошка'
              : info.animal_kind.kind === 'dog'
              ? 'Собака'
              : info.animal_kind.kind === 'bird'
              ? 'Птица'
              : 'Медведь'
          }} {{ Math.floor(info.age) + formatAgeString(info.age) }}
        </span>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'MainPage',
  data() {
    return {
      animal_kinds: [],
      animals: [],
      isActive: false,
      info: null
    }
  },
  computed: {
    container: function () {
      return document.getElementById( 'container' )
    },

  },
  async mounted() {
    axios.defaults.baseURL = process.env.VUE_APP_API_URL
    this.animal_kinds = await this.getAnimalKinds();
    this.animals = await this.getAnimals();
    this.calcSize(this.animals);
    this.initListenChannel();
  },
  beforeUnmount() {
    // eslint-disable-next-line
    Echo.leave('animals');
  },
  methods: {
    getImgUrl(pet) {
      var images = require.context('../assets/', false, /\.png$/)
      return images('./' + pet + ".png")
    },
    getAnimalKinds: async function () {
      const response = await axios.get('animal_kinds');

      return response.data;
    },
    getAnimals: async function () {
      const response = await axios.get('animals');

      return response.data;
    },
    toggleActive: function () {
      if ( this.isActive ) {
        this.container.classList.remove( 'isActive' )
        this.isActive = !this.isActive
      } else {
        this.container.classList.add( 'isActive' )
        this.isActive = !this.isActive
      }
    },
    createAnimal: async function (id) {
      const response = await axios.post('/animal/create', {animal_kind_id: id});
      this.animals.push(response.data.animal);
      this.calcSize(this.animals);
    },
    calcSize: function (animals) {
      const maxWidth = 240;
      setTimeout(() => {
        animals.map(animal => {
          const size = animal.size == 0 || Math.floor(maxWidth * animal.size / animal.animal_kind.max_size * Number(animal.animal_kind.growth_factor)) < 30
              ? '30'
              : Math.floor(maxWidth * animal.size / animal.animal_kind.max_size * Number(animal.animal_kind.growth_factor)) > 240
              ? 240
              : Math.floor(maxWidth * animal.size / animal.animal_kind.max_size * Number(animal.animal_kind.growth_factor))
          document.getElementById(animal.animal_kind.kind).setAttribute('style', `width: ${size}px`)
        })
      }, 1);
    },
    isExist(animalKind) {
      return this.animals.find(animal => animal.animal_kind.id === animalKind.id)
    },
    initListenChannel: function () {
      // eslint-disable-next-line
      Echo
        .channel('laravel_database_animals')
        .listen('.AnimalAgeUp', (data) => {
          this.animals = this.animals.map(animal => {
            if (animal.id == data.animal.id) {
              animal = {...animal, age: data.animal.age, size: data.animal.size}
            }

            return animal;
          })
          this.calcSize(this.animals);
        })
        .listen('.AnimalKill', (data) => {
          axios.delete(`/animals/${data.animal.id}`)
          // eslint-disable-next-line
          this.animals = this.animals.filter(animal => {
            return animal.id != data.animal.id;
          })
        })
    },
    toGetOld(kindId) {
      const animal = this.animals.find(animal => animal.animal_kind.id === kindId)
      axios.patch(`/animals/${animal.id}`)
    },
    async getInfo(id) {
      const response = await axios.get(`/animals/${id}`);
      this.info = response.data.data;
      setTimeout(() => {
        this.info = null;
      }, 2000)
    },
    formatAgeString(age) {
      age = Math.floor(age)
      if (age == 1) {
        return ' год'
      } else if (age <= 4 && age > 1) {
        return ' года'
      } else {
        return ' лет'
      }
    },
  },
}
</script>

<style lang="scss">
  .info {
    position: absolute;
    bottom: 15%;
    left: 50%;
    transform: translate(-50%,-50%);
  }
  .disabled {
    opacity: .5;

    &:after {
      content: '×';
      font-size: 44px;
      position: absolute;
      top: 42.5%;
      left: 50%;
      transform: translate(-50%,-50%);
    }
  }

  .animals {
    display: flex;
    align-items: center;
    gap: 50px;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%,-50%);
  }

  .content {
    width: 100vw;
    height: 100vh;
    position: relative;
  }

  .animal {
    width: 30px;
    height: auto;
    cursor: pointer;
  }

  .icon {
    width: 20px;
    height: 20px;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%,-50%);
  }

  .dashboard {
    width: 100vw;
    height: 100vh;
    background: linear-gradient(180deg, #E0F7FA 0%, #80DEEA 100%);
    position: relative;
  }

  .container {
    width: 50px;
    height: 50px;
    position: absolute;
    top: 50px;
    left: 50px;
    transform: translate( -50%, -50% );
    z-index: 100;
  }

  .expandButton,
  .item {
    width: 100%;
    height: 100%;
    background: #FF4878;
    border-radius: 50%;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate( -50%, -50% );
    transition: all 0.6s cubic-bezier( 0.680, -0.550, 0.265, 1.550 );
  }

  .expandButton {
    z-index: 1;
    position: relative;
    cursor: pointer;
  }

  .expandButton svg {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%,-50%);
  }

  .expandButton:before {
    content: '';
    display: block;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    position: absolute;
    background:  rgba(255,72,120, .5);
    top: 50%;
    left: 50%;
    transform: translate( -50%, -50% );
    transition: all 0.6s cubic-bezier( 0.680, -0.550, 0.265, 1.550 );
  }

  .item {
    width: 75%;
    height: 75%;
    background: #19D1E7;
    transition-delay: 0s;
    border: 1px solid #1a202c;
    //position: relative;
    cursor: pointer;
  }

  .container.isActive .expandButton {
    transform: translate( -50%, -50% ) rotate( 90deg );
  }

  .container.isActive .expandButton:before {
    transform: translate( -50%, -50% ) scale( 1.4 );
  }

    $var: 200;
    $var2: 0.25;

    @for $i from 2 through 15 {
      .container.isActive .item:nth-child(#{$i}) {
        top: 50%;
        left: #{$var} + '%';
        transition-delay: #{$var2}s;
      }

      $var: $var + 100;
      $var2: $var2 - 0.05;
    }
</style>
