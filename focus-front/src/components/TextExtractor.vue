<template>
    <div>
      <!-- Display the most common pattern -->
      <p>Plate number: {{ mostCommonPattern }}</p>
      <!-- Display the most frequent color -->
      <p>Color: {{ mostFrequentColor }}</p>
      <!-- Display the most frequent vehicle type -->
      <p>Vehicle Type: {{ mostFrequentVehicleType }}</p>
      <!-- Show a preview of the clear image -->
      <div v-if="selectedImage">
        <p>Clear Plate Image:</p>
        <img :src="selectedImage" alt="Clear plate" style="max-width: 100%; height: auto;" />
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  export default {
    name: "TextExtractor",
    props: {
      plates: {
        type: Array,
        default: () => [],
      },
      sendForAnalysis: {
        type: Boolean,
        default: false, // Default to false to prevent unintended analysis
      },
    },
    data() {
      return {
        extractedText: null,
        tempWords: [],
        getArrayOfLetters: null,
        mostCommonPattern: null,
        mostFrequentColor: null,
        mostFrequentVehicleType: null,
        selectedImage: null, // For storing the clearest plate image
        clearPlatesTimer: null, // Timer to clear the plates array after 10 seconds
      };
    },
    watch: {
      plates(newPlates) {
        if (newPlates.length > 1 && this.sendForAnalysis) {
          this.analyzeData();
          this.resetPlatesAfterDelay();
        }
      },
      sendForAnalysis(newSignal) {
        if (newSignal && this.plates.length > 1) {
          this.analyzeData();
          this.resetPlatesAfterDelay();
        }
      },
    },
    methods: {
      analyzeData() {
        if (!this.plates.length) {
          console.warn("No data to analyze.");
          return;
        }
  
        // Concatenate all text data
        const concatenatedText = this.plates
          .map((item) => item.text.replace(/[-_=.,~()]/g, " "))
          .join(" ");
        this.extractPattern(concatenatedText);
        this.analyzeColors();
        this.findMostFrequentVehicleType();
        this.selectClearImage();
  
        // Save data to localStorage
        if (this.mostCommonPattern && this.mostFrequentColor && this.mostFrequentVehicleType && this.selectedImage) {
          this.saveToDatabase(
            this.mostCommonPattern,
            this.mostFrequentColor,
            this.mostFrequentVehicleType,
            this.selectedImage
          );
        }
      },
  
      extractPattern(concatenatedText) {
        let foundText = "";
        let tempWord = "";
        let isValidPattern = false;
  
        for (let i = 0; i < concatenatedText.length; i++) {
          let char = concatenatedText[i];
          tempWord += char;
          this.tempWords.push(tempWord);
  
          if (tempWord.length >= 9) {
            if (/^[A-Za-z]{5}\s\d+[A-Za-z]$/.test(tempWord)) {
              foundText = tempWord;
              isValidPattern = true;
              break;
            }
            tempWord = tempWord.slice(1);
          }
        }
  
        this.extractedText = isValidPattern ? foundText : "No match found";
        this.getArrayOfLetters = this.tempWords;
        this.findMostCommonPattern();
      },
  
      findMostCommonPattern() {
        const count = {};
        this.getArrayOfLetters.forEach((item) => {
          count[item] = (count[item] || 0) + 1;
        });
  
        let maxCount = 0;
        let mostCommon = null;
        for (const pattern in count) {
          if (count[pattern] > maxCount) {
            maxCount = count[pattern];
            mostCommon = pattern;
          }
        }
  
        this.mostCommonPattern = mostCommon;
      },
  
      analyzeColors() {
        const colorCounts = {};
        this.plates.forEach((item) => {
          const color = item.color;
          colorCounts[color] = (colorCounts[color] || 0) + 1;
        });
  
        let maxCount = 0;
        let frequentColor = null;
  
        for (const color in colorCounts) {
          if (colorCounts[color] > maxCount) {
            maxCount = colorCounts[color];
            frequentColor = color;
          }
        }
  
        this.mostFrequentColor = frequentColor;
      },
  
      findMostFrequentVehicleType() {
        const vehicleCounts = {};
        this.plates.forEach((item) => {
          const type = item.vehicleType;
          vehicleCounts[type] = (vehicleCounts[type] || 0) + 1;
        });
  
        let maxCount = 0;
        let frequentType = null;
  
        for (const type in vehicleCounts) {
          if (vehicleCounts[type] > maxCount) {
            maxCount = vehicleCounts[type];
            frequentType = type;
          }
        }
  
        this.mostFrequentVehicleType = frequentType;
      },
  
      selectClearImage() {
        const clearImage = this.plates.reduce((best, item) => {
          if (!best || item.imageQuality > best.imageQuality) {
            return item;
          }
          return best;
        }, null);
  
        this.selectedImage = clearImage ? clearImage.image : null;
      },
      async saveToDatabase(pattern, color, vehicleType, image) {
  console.log("Saving data to API");
  const apiUrl = `${import.meta.env.VITE_API_BASE_URL}/vehicle-records`;
  const timestamp = new Date().toISOString();
  
  const formData = new FormData();
  formData.append('pattern', this.mostCommonPattern);
  formData.append('color', color);
  formData.append('vehicleType', vehicleType);
  formData.append('image', image);  // If 'image' is a file object
  formData.append('timestamp', timestamp);
  formData.append('user_id', JSON.parse(localStorage.getItem('user')).id);


 
  try {
    const response = await axios.post(apiUrl, formData, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('access_token_employer')}`,
        'Content-Type': 'multipart/form-data',  // This is required for file uploads
      },
    });

    console.log("Data successfully saved:", response.data);
  } catch (error) {
    console.error("Error saving data to API:", error);
  }
},

  
      resetPlatesAfterDelay() {
        if (this.clearPlatesTimer) {
          clearTimeout(this.clearPlatesTimer);
        }
  
        this.clearPlatesTimer = setTimeout(() => {
          console.log("Plates cleared after 10 seconds.");
          this.extractedText = null;
          this.tempWords = [];
          this.getArrayOfLetters = null;
          this.mostCommonPattern = null;
          this.mostFrequentColor = null;
          this.mostFrequentVehicleType = null;
          this.selectedImage = null;
          this.$emit("clear-plates");
        }, 1000); // 10 seconds delay
      },
    },
  };
  </script>
  
  <style scoped>
  img {
    max-width: 100%;
    height: auto;
  }
  </style>
  
