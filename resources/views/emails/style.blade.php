<style>
 body {
  width: 100%;
  margin: 0;
  padding: 0;
  position: relative;
  height: 100%;
  background: #f7f8fa;
  font-family: "Inter", sans-serif;
}

button {
  padding: 10px;
  border-radius: 8px;
  font-size: 1em;
  border: 0;
  outline: none;
  cursor: pointer;
}

h1 {
  font-size: 30px;
}

.header {
  background: #fad0fc;
  width: 100%;
  min-height: 140px;
  max-width: 1728px;
  margin: 0 auto;
  padding: 0 10%;
  position: relative;
  z-index: 1;
}

.body {
  width: 100%;
  min-height: 100vh;
  max-width: 1728px;
  margin: 0 auto;
  padding: 0 10%;
  z-index: 200;
  position: relative;
}

.footer {
  width: 100%;
  max-width: 1728px;
  margin: 0 auto;
  padding: 0 10%;
  z-index: 201;
  position: relative;
  margin-top: 50px;
  font-size: 12px;
}

.w-100pc {
  width: 100%;
}

.max-w-600px {
  max-width: 600px;
}

.text-right {
  text-align: right;
}

.text-white {
  color: #fff;
}

.text-black {
  color: #000;
}

.bg-white {
  background: #ffffff;
}

.fix-center {
  margin: 0 auto;
}

.rounded-8px {
  border-radius: 8px;
}

.box-shadow-body {
  box-shadow: 0px 10px 10px -5px rgba(0, 0, 0, 0.04), 0px 20px 25px -5px rgba(0, 0, 0, 0.1);
  margin-bottom: 50px;
}

.mt--40px {
  margin-top: -40px;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
  .header,
  .body,
  .footer {
      padding: 0 5%;
  }
}

@media (max-width: 520px) {
  .text-right {
      text-align: center;
  }

  button {
      font-size: 0.9em;
      padding: 8px;
  }

  .max-w-600px {
      width: 100%;
  }

  .logo {
      width: 150px;
      height: auto;
  }

  .body-table {
      width: calc(100% - 40px);
      padding: 20px;
  }

  h1 {
      font-size: 5vw;
  }
}

@media (max-width: 420px) {
  .body-table {
      padding: 20px 10px;
  }

  .logo {
      width: 120px;
  }

  h1 {
      font-size: 6vw;
  }

  button {
      font-size: 0.8em;
      padding: 6px;
  }
}

  </style>