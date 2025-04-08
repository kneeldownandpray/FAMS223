<template>{{ this.linkenv }}{{ resumeData2.user.profile_picture }}
  <div class="q-pa-md q-bg-primary" style="">
    <q-btn push color="white" text-color="primary" @click="generatePDF" label="Download Resume (PDF)" />
  </div>
</template>

<script>
import { PDFDocument, StandardFonts, rgb } from 'pdf-lib';
import { saveAs } from 'file-saver';
const ImageBaseUrl = import.meta.env.VITE_IMG_DP;
export default {

  props: {
    resumeData2: {
      type: Object, // or the appropriate type for your data
      required: true
    }
  },
  data() {
    return {
      linkenv:null,
      imgsourcelink:"http://192.168.0.34:8090/api/image/NZsdfLtchlpxNjz8lipC5I0YEEPQiNKah2CpKQIX.jpg",
      // resumeData: {
      //   name: "Maria Clara Santos",
      //   address: "Quezon City, Philippines",
      //   mobileNumber: "+639171234567",
      //   email: "maria.santos@example.com",
      //   summary: "A dedicated and skilled software developer with over 5 years of experience in developing web applications and software solutions.",
      //   age: 28,
      //   date: "1995-08-15",
      //   gender: "Female",
      //   civilstatus: "Married",
      //   height: "5'6\"",
      //   weight: "60kg",
      //   nationality: "Filipino",
      //   religion: "Catholic",
      //   education: [
      //     { educationtype: "Tertiary", tertiary: "Ateneo de Manila University", course: "Bachelor of Science in Information Technology", year: "2012 - 2016" },
      //     { educationtype: "Senior Hs", tertiary: "Philippine Science High School", course: "Science, Technology, Engineering, and Mathematics", year: "2010 - 2012" },
      //     { educationtype: "Secondary", tertiary: "Quezon City Science High School", course: "", year: "2006 - 2010" },
      //     { educationtype: "Tertiary", tertiary: "University of the Philippines", course: "Master of Science in Computer Science", year: "2017 - 2019" },
      //     { educationtype: "Certification", tertiary: "Coursera", course: "Full Stack Web Development", year: "2020" },
      //   ],
      //   workExperience: [
      //     { companyname: "Innovative Solutions Inc.", companyaddress: "456 Innovation Drive", position: "Senior Software Developer", date: "2019-2023", jobdescription: ["Led a team of developers in creating web applications", "Implemented RESTful APIs", "Conducted code reviews and mentoring"] },
      //     { companyname: "Tech Innovators", companyaddress: "789 Tech Avenue", position: "Software Developer", date: "2016-2019", jobdescription: ["Developed and maintained web applications", "Collaborated with cross-functional teams", "Participated in agile development processes"] },
      //     { companyname: "Creative Minds", companyaddress: "123 Creative Street", position: "Junior Developer", date: "2015-2016", jobdescription: ["Assisted in developing web applications", "Performed testing and debugging", "Maintained documentation"] },
      //     { companyname: "Future Tech", companyaddress: "321 Future Road", position: "Intern", date: "2014-2015", jobdescription: ["Supported senior developers", "Learned and applied new technologies", "Participated in team meetings"] },
      //   ],
      //   skills: ["JavaScript", "Node.js", "Vue.js", "React", "Python", "Django", "HTML", "CSS", "SQL", "Git"],
      //   certificates: ["Certified JavaScript Developer", "Certified Python Developer", "AWS Certified Solutions Architect", "Certified ScrumMaster"],
      // },
      resumeData: {
        name: "",
        address: "",
        mobileNumber: "",
        email: "",
        summary: "",
        gender: "",
        civilstatus: "",
        age: "",
        date: "",
        height: "",
        weight: "",
        nationality: "",
        religion: "",
        education: [
          { educationtype: "", tertiary: "", course: "", year: "" },
          
        ],
        workExperience: [
          { companyname: "", companyaddress: "", position: "", date: "", jobdescription: [] },
        ],
        skills: [],
        certificates: [],
       
      
      },


    };
  },
  mounted() {
  if (this.resumeData2) {
    this.populateResumeData(this.resumeData2); 
  }
},
  methods: {

    populateResumeData(json) {
      this.linkenv = ImageBaseUrl;
    this.resumeData.name = json.full_name || "";
    this.resumeData.address = json.address || "";
    this.resumeData.mobileNumber = json.contact_no || "";
    this.resumeData.email = json.user?.email || "";
    this.resumeData.summary = json.objectives || "";
    this.resumeData.age = json.user?.birthday ? this.calculateAge(json.user.birthday) : null;
    this.resumeData.date =json.user.birthday || "";
    this.resumeData.gender = json.user?.gender || "";
    this.resumeData.civilstatus = json.civil_status || "";
    this.resumeData.height = json.height ? String(json.height) : "";
    this.resumeData.weight = json.weight ? String(json.weight) : "";
    this.resumeData.nationality = json.nationality || "";
    this.resumeData.religion = json.religion || "";
    this.resumeData.civilstatus = json.civil_status || "";

    // Education
    this.resumeData.education = json.educational_attainments.map(edu => ({
      educationtype: edu.level || "",
      tertiary: edu.institution || "",
      course: edu.course || "",
      year: edu.period || ""
    }));

    // Work Experience
    this.resumeData.workExperience = json.work_experiences.map(work => ({
      companyname: work.company_name || "",
      companyaddress: work.company_address || "",
      position: work.position || "",
      date: `${work.start_date} to ${work.end_date}`,
      jobdescription: work.job_descriptions.map(desc => desc.description)
    }));

    // Skills
    this.resumeData.skills = json.skills.map(skill => skill.skill_name);

    // Certificates

    if (json.certifications && Array.isArray(json.certifications)) {
      // Map through the certifications and extract the certificate_name, filter out any empty strings
      this.resumeData.certificates = json.certifications
        .map(cert => cert.certificate_name || "")  // Extract certificate_name
        .filter(cert => cert);  // Remove any empty strings or undefined values
    } else {
      this.resumeData.certificates = [];  // Default to empty array if certifications is not valid
    }

  },

  calculateAge(birthday) {
    const birthDate = new Date(birthday);
    const today = new Date();
    let age = today.getFullYear() - birthDate.getFullYear();
    const m = today.getMonth() - birthDate.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
      age--;
    }
    return age;
  },
    async generatePDF() {

      const templatePath = '/templates/sputum.pdf';
      const imagePath = this.imgsourcelink;

      try {
        const [existingPdfBytes, imageBytes] = await Promise.all([
          fetch(templatePath).then(res => res.arrayBuffer()),
          fetch(imagePath).then(res => res.arrayBuffer())
        ]);

        const pdfDoc = await PDFDocument.load(existingPdfBytes);
        const image = await pdfDoc.embedJpg(imageBytes);
        const helveticaFont = await pdfDoc.embedFont(StandardFonts.Helvetica);
        const helveticaBoldFont = await pdfDoc.embedFont(StandardFonts.HelveticaBold);
        let page1 = pdfDoc.getPages()[0];

        const imageWidth = 100;
        const imageHeight = 100;
        const imageX = (page1.getWidth() - imageWidth) / 2;
        const imageY = 450;

        // Draw the image
        page1.drawImage(image, { x: imageX, y: imageY, width: imageWidth, height: imageHeight });

        // Draw the border
        page1.drawRectangle({
          x: imageX - 5,
          y: imageY - 5,
          width: imageWidth + 10,
          height: imageHeight + 10,
          borderColor: rgb(0, 0, 0),
          borderWidth: 3,
        });

        // Draw personal details
        this.addPersonalDetails(page1, helveticaFont, helveticaBoldFont);

        let yOffset = 210; // Adjust this value to start at the bottom of the first page
        let page = page1;
        ({ page, yOffset } = this.addEducationSectionToPDF(page, "EDUCATIONAL ATTAINMENT:", this.resumeData.education, yOffset, pdfDoc, helveticaBoldFont, helveticaFont));
        ({ page, yOffset } = this.addSectionToPDF(page, "EMPLOYMENT REFERENCES:", this.resumeData.workExperience, yOffset, pdfDoc, helveticaBoldFont, helveticaFont));
        ({ page, yOffset } = this.addSectionToPDF(page, "SKILLS:", this.resumeData.skills, yOffset, pdfDoc, helveticaBoldFont, helveticaFont, true));
        ({ page, yOffset } = this.addSectionToPDF(page, "CERTIFICATES:", this.resumeData.certificates, yOffset, pdfDoc, helveticaBoldFont, helveticaFont, true));

        const pdfBytes = await pdfDoc.save();
        saveAs(new Blob([pdfBytes], { type: 'application/pdf' }), 'resume.pdf');
      } catch (error) {
        console.error('Error generating PDF:', error);
      }
    },

    addPersonalDetails(page, helveticaFont, helveticaBoldFont) {
      const { name, address, mobileNumber, email } = this.resumeData;
      const textY = 600;

      // Draw the name in bold
      this.drawCenteredText(page, name, textY, 15, helveticaBoldFont);

      // Draw the address, mobile number, and email
      const details = [
        address ? `Address: ${address}` : '',
        mobileNumber ? `Mobile Number: ${mobileNumber}` : '',
        email ? `Email Add: ${email}` : ''
      ].filter(Boolean).join('\n');

      const lines = details.split('\n');
      lines.forEach((line, index) => {
        this.drawCenteredText(page, line, textY - 15 - (index * 12), 12, helveticaFont);
      });

      // Set other form fields
      const form = page.doc.getForm();
      form.getTextField('summary').setText(this.resumeData.summary);
      form.getTextField('age').setText(String(this.resumeData.age));
      form.getTextField('date').setText(this.resumeData.date);
      form.getTextField('gender').setText(this.resumeData.gender);
      form.getTextField('civilstatus').setText(this.resumeData.civilstatus);
      form.getTextField('height').setText(this.resumeData.height);
      form.getTextField('weight').setText(this.resumeData.weight);
      form.getTextField('nationality').setText(this.resumeData.nationality);
      form.getTextField('religion').setText(this.resumeData.religion);
    },

    drawCenteredText(page, text, y, size, font) {
      const textWidth = font.widthOfTextAtSize(text, size);
      const textX = (page.getWidth() - textWidth) / 2;
      page.drawText(text, {
        x: textX,
        y: y,
        size: size,
        font: font,
        color: rgb(0, 0, 0),
      });
    },

    addEducationSectionToPDF(page, title, data, yOffset, pdfDoc, boldFont, regularFont) {
      const margin = 72;
      const pageHeight = 792; // 27.94cm in points
      const lineHeight = 15;
      const sectionSpacing = 20;
      const labelWidth = 150; // Adjust this value to align colons

      page.drawText(title, { x: margin, y: yOffset, size: 12, font: boldFont });
      yOffset -= sectionSpacing;

      data.forEach(item => {
        if (yOffset < margin + lineHeight) {
          page = pdfDoc.addPage([612, pageHeight]); // 21.59cm x 27.94cm in points
          yOffset = pageHeight - margin;
        }
        const labelX = margin;
        const valueX = margin + 105; // Adjust this value to align colons

        page.drawText(`${item.educationtype}`, { x: labelX, y: yOffset, size: 12, font: regularFont });
        page.drawText(`:`, { x: 144, y: yOffset, size: 12, font: regularFont });
        page.drawText(item.tertiary, { x: valueX, y: yOffset, size: 12, font: regularFont });
        yOffset -= lineHeight;

        const courseLabel = item.educationtype === "Senior Hs" ? "Strand" : "Course";
        if (item.course) {
          page.drawText(`${courseLabel}`, { x: labelX, y: yOffset, size: 12, font: regularFont });
          page.drawText(`:`, { x: 144, y: yOffset, size: 12, font: regularFont });
          page.drawText(item.course, { x: valueX, y: yOffset, size: 12, font: regularFont });
          yOffset -= lineHeight;
        }

        page.drawText(item.year, { x: valueX, y: yOffset, size: 12, font: regularFont });
        yOffset -= lineHeight * 2; // Add extra line break after each education entry
      });

      return { page, yOffset: yOffset - sectionSpacing };
    },

    addSectionToPDF(page, title, data, yOffset, pdfDoc, boldFont, regularFont, isList = false) {
      const margin = 72;
      const pageHeight = 792; // 27.94cm in points
      const lineHeight = 15;
      const sectionSpacing = 20;
      const labelWidth = 150; // Adjust this value to align colons

      page.drawText(title, { x: margin, y: yOffset, size: 12, font: boldFont });
      yOffset -= sectionSpacing;

      data.forEach(item => {
        if (isList) {
          if (yOffset < margin + lineHeight) {
            page = pdfDoc.addPage([612, pageHeight]); // 21.59cm x 27.94cm in points
            yOffset = pageHeight - margin;
          }
          page.drawText(`• ${item}`, { x: margin, y: yOffset, size: 12, font: regularFont });
          yOffset -= lineHeight;
        } else {
          const fields = {
            companyname: "Company Name",
            companyaddress: "Company Address",
            position: "Position",
            date: "Inclusive Dates",
            jobdescription: "JOB DESCRIPTIONS"
          };

          Object.entries(item).forEach(([key, value]) => {
            if (yOffset < margin + lineHeight) {
              page = pdfDoc.addPage([612, pageHeight]); // 21.59cm x 27.94cm in points
              yOffset = pageHeight - margin;
            }
            if (Array.isArray(value)) {
              if (key === 'jobdescription') {
                yOffset -= lineHeight; // Add a line break before "JOB DESCRIPTIONS"
              }
              page.drawText(`${fields[key]}:`, { x: margin, y: yOffset, size: 12, font: key === 'jobdescription' ? boldFont : regularFont });
              yOffset -= lineHeight;
              value.forEach(subItem => {
                if (yOffset < margin + lineHeight) {
                  page = pdfDoc.addPage([612, pageHeight]); // 21.59cm x 27.94cm in points
                  yOffset = pageHeight - margin;
                }
                page.drawText(`• ${subItem}`, { x: margin + 20, y: yOffset, size: 12, font: regularFont });
                yOffset -= lineHeight;
              });
            } else {
              const labelX = margin;
              const valueX = margin + 150; // Adjust this value to align colons
              
              page.drawText(`${fields[key]}`, { x: labelX, y: yOffset, size: 12, font: regularFont });
              page.drawText(`:`, { x: 185, y: yOffset, size: 12, font: regularFont });
              page.drawText(String(value), { x: valueX, y: yOffset, size: 12, font: key === 'companyname' ? boldFont : regularFont });
              yOffset -= lineHeight;
            }
          });
          yOffset -= sectionSpacing;
        }
      });

      return { page, yOffset: yOffset - sectionSpacing };
    },
  },
};
</script>

<style scoped>
.q-page {
  height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>